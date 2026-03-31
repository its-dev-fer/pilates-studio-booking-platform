<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CreditPackage;
use App\Models\CreditPackagePurchase;
use App\Models\CreditPurchaseRequest;
use App\Models\UserCredit;
use App\Support\CreditPackagePromotionPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Inicia la sesión de Checkout en Stripe (Cobro Único).
     */
    public function process(Request $request, CreditPackage $package)
    {
        if ($request->user()->hasRole(['admin', 'empleado'])) {
            return redirect('/dashboard');
        }

        $hasPendingRequest = CreditPurchaseRequest::query()
            ->where('user_id', $request->user()->id)
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->exists();

        if ($hasPendingRequest) {
            return redirect()
                ->route('checkout.credits')
                ->with('error', 'Ya tienes una solicitud pendiente. Espera la validación antes de realizar otra compra.');
        }

        $activeCredits = $request->user()->credits()
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');

        if ($activeCredits > 0) {
            return redirect()
                ->route('checkout.credits')
                ->with('error', 'No puedes comprar otro paquete mientras tengas créditos activos.');
        }

        $alreadyPurchased = $package->is_one_time_purchase
            && CreditPackagePurchase::query()
                ->where('user_id', $request->user()->id)
                ->where('credit_package_id', $package->id)
                ->exists();

        if ($alreadyPurchased) {
            return redirect()
                ->route('checkout.credits')
                ->with('error', 'Este paquete es de compra única y ya fue adquirido en tu cuenta.');
        }

        try {
            $items = CreditPackagePromotionPricing::checkoutLineItems($package, now(), $request->user());
        } catch (Throwable $e) {
            return redirect()
                ->route('checkout.credits')
                ->with('error', $e->getMessage());
        }

        return $request->user()->checkout($items, [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}&package_id='.$package->id,
            'cancel_url' => route('checkout.cancel'),
        ]);
    }

    public function success(Request $request)
    {
        $user = $request->user();
        $packageId = $request->get('package_id');
        $sessionId = $request->get('session_id');

        // Validar que exista la sesión en Stripe (Para evitar accesos directos maliciosos)
        $stripeSession = $user->stripe()->checkout->sessions->retrieve($sessionId);
        if ($stripeSession->payment_status !== 'paid') {
            return redirect()->route('checkout.credits')->withError('El pago no fue procesado.');
        }

        $package = CreditPackage::findOrFail($packageId);

        $alreadyPurchased = false;

        // Iniciar transacción de BD para asegurar la integridad
        DB::transaction(function () use ($user, $package, &$alreadyPurchased) {
            if ($package->is_one_time_purchase) {
                $purchase = CreditPackagePurchase::firstOrCreate([
                    'user_id' => $user->id,
                    'credit_package_id' => $package->id,
                ]);

                if (! $purchase->wasRecentlyCreated) {
                    $alreadyPurchased = true;

                    return;
                }
            }

            // 1. Otorga los créditos al usuario (Por defecto, los asociamos al tenant 1 o al que el usuario seleccionó.
            // Según tus reglas, los créditos son por sucursal. Si la compra es global para reservar donde sea,
            // puedes requerir que el usuario elija sucursal, pero aquí usaremos el tenant de la cita pendiente si existe)

            $pendingAppointment = session('pending_appointment');
            $tenantId = $pendingAppointment ? $pendingAppointment['tenant_id'] : $user->tenants()->first()->id;

            $userCredit = UserCredit::create([
                'user_id' => $user->id,
                'tenant_id' => $tenantId, // Crédito ligado a la sucursal de interés
                'balance' => $package->credits_amount,
                'expires_at' => now()->addDays(30), // Regla: 30 días de caducidad
                'is_special' => false,
            ]);

            // 2. Procesar Cita Pendiente (Si el usuario viene de la landing)
            if ($pendingAppointment) {

                // Consumir 1 crédito
                $userCredit->decrement('balance', 1);

                // Crear la cita oficial
                Appointment::create([
                    'tenant_id' => $pendingAppointment['tenant_id'],
                    'user_id' => $user->id,
                    'date' => $pendingAppointment['date'],
                    'time_slot' => $pendingAppointment['time_slot'],
                    'status' => 'scheduled',
                ]);

                // Limpiar la intención de sesión
                session()->forget('pending_appointment');

                // TODO: Lanzar Evento para enviar email de confirmación (Notificaciones)
            }
        });

        if ($alreadyPurchased) {
            return redirect()
                ->route('checkout.credits')
                ->with('error', 'Este paquete es de compra única y ya fue adquirido en tu cuenta.');
        }

        // Redirigir al Panel de Clientes (App Panel de Filament)
        return redirect('/clientes')->with('success', '¡Pago exitoso! Tus créditos han sido abonados.');
    }

    public function cancel()
    {
        return redirect()->route('checkout.credits')->with('error', 'El pago ha sido cancelado. Puedes intentarlo de nuevo.');
    }
}

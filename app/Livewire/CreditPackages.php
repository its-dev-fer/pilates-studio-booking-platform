<?php

namespace App\Livewire;

use App\Models\CreditPackage;
use App\Models\CreditPackagePurchase;
use App\Models\CreditPurchaseRequest;
use App\Models\User;
use App\Support\CreditPackagePromotionPricing;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreditPackages extends Component
{
    public $packages;

    /** @var array<int, array{base_price: float, final_price: float, promotion: mixed}> */
    public array $pricingByPackageId = [];

    public $activeCredits = 0;

    public $hasPendingPurchaseRequest = false;

    public function mount()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            $this->packages = collect();
            $this->activeCredits = 0;

            return;
        }

        if ($user->hasRole(['admin', 'empleado'])) {
            $this->redirect('/dashboard');

            return;
        }

        $this->packages = CreditPackage::query()
            ->where(function ($query) use ($user) {
                $query->where('is_one_time_purchase', false)
                    ->orWhereDoesntHave('purchases', function ($purchaseQuery) use ($user) {
                        $purchaseQuery->where('user_id', $user->id);
                    });
            })
            ->get();

        $this->pricingByPackageId = [];
        foreach ($this->packages as $package) {
            $this->pricingByPackageId[$package->id] = CreditPackagePromotionPricing::resolve($package);
        }

        $this->activeCredits = $user->credits()
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');

        $this->hasPendingPurchaseRequest = CreditPurchaseRequest::query()
            ->where('user_id', $user->id)
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->exists();
    }

    public function requestManualPurchase(int $packageId, string $paymentMethod): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            session()->flash('error', 'Debes iniciar sesión para continuar.');

            return;
        }

        if (! in_array($paymentMethod, [CreditPurchaseRequest::METHOD_TRANSFER, CreditPurchaseRequest::METHOD_CASH], true)) {
            session()->flash('error', 'Método de pago inválido.');

            return;
        }

        $hasPendingRequest = CreditPurchaseRequest::query()
            ->where('user_id', $user->id)
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->exists();

        if ($hasPendingRequest) {
            session()->flash('error', 'Ya tienes una solicitud pendiente. Espera la validación de un administrador o empleado.');

            return;
        }

        $activeCredits = $user->credits()
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');

        if ($activeCredits > 0) {
            session()->flash('error', 'No puedes solicitar otro paquete mientras tengas créditos activos.');

            return;
        }

        $package = CreditPackage::find($packageId);
        if (! $package) {
            session()->flash('error', 'El paquete seleccionado no existe.');

            return;
        }

        $alreadyPurchased = $package->is_one_time_purchase
            && CreditPackagePurchase::query()
                ->where('user_id', $user->id)
                ->where('credit_package_id', $package->id)
                ->exists();

        if ($alreadyPurchased) {
            session()->flash('error', 'Este paquete es de compra única y ya fue adquirido en tu cuenta.');

            return;
        }

        $alreadyPending = CreditPurchaseRequest::query()
            ->where('user_id', $user->id)
            ->where('credit_package_id', $package->id)
            ->where('payment_method', $paymentMethod)
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->exists();

        if ($alreadyPending) {
            session()->flash('error', 'Ya tienes una solicitud pendiente para este paquete y método de pago.');

            return;
        }

        $pendingAppointment = session('pending_appointment');
        $pricing = CreditPackagePromotionPricing::resolve($package);

        CreditPurchaseRequest::create([
            'user_id' => $user->id,
            'credit_package_id' => $package->id,
            'quoted_base_price' => $pricing['base_price'],
            'quoted_final_price' => $pricing['final_price'],
            'payment_method' => $paymentMethod,
            'status' => CreditPurchaseRequest::STATUS_PENDING,
            'requested_tenant_id' => $pendingAppointment['tenant_id'] ?? null,
            'requested_date' => $pendingAppointment['date'] ?? null,
            'requested_time_slot' => $pendingAppointment['time_slot'] ?? null,
        ]);

        session()->flash(
            'success',
            'Solicitud enviada. Un administrador o empleado validará tu pago para acreditar tus créditos.'
        );
    }

    public function render()
    {
        return view('livewire.credit-packages')->layout('layouts.app');
    }
}

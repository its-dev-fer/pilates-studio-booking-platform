<?php

namespace App\Http\Middleware;

use App\Models\Appointment;
use App\Models\UserCredit;
use Carbon\Carbon;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessPendingBooking
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && session()->has('pending_booking')) {
            
            $booking = session('pending_booking');
            $user = auth()->user();
            $formattedTime = \Carbon\Carbon::parse($booking['time_slot'])->format('H:i');

            // 1. Validar que no tenga ya esa misma cita
            $existingAppointment = Appointment::where('user_id', $user->id)
                ->whereDate('date', $booking['date'])
                ->where('time_slot', 'like', $formattedTime . '%')
                ->where('status', 'scheduled')
                ->exists();

            if ($existingAppointment) {
                Notification::make()
                    ->title('Ya estabas en esta clase')
                    ->body('Notamos que intentaste reservar una clase en la que ya tienes un lugar asegurado.')
                    ->warning()
                    ->persistent()
                    ->send();
                    
                session()->forget('pending_booking');
                return $next($request);
            }

            // 2. VALIDACIÓN DE CRÉDITOS
            // (Ajusta los nombres de los campos de UserCredit según tu migración real)
            $activeCredit = UserCredit::where('user_id', $user->id)
                ->where('balance', '>', 0) // Buscamos si tiene créditos disponibles
                ->first();

            if ($activeCredit) {
                // 3A. TIENE CRÉDITOS: Descontar y Agendar
                $activeCredit->decrement('balance'); // Le restamos 1 crédito

                Appointment::create([
                    'tenant_id' => $booking['tenant_id'],
                    'user_id' => $user->id,
                    'date' => $booking['date'],
                    'time_slot' => $booking['time_slot'],
                    'status' => 'scheduled',
                    'check_in_status' => 'pendiente', // Ya está cubierta por su paquete
                    'payment_method' => 'credit_balance',
                    'booking_origin' => 'landing_pending_booking',
                ]);

                Notification::make()
                    ->title('¡Reserva Confirmada!')
                    ->body('Se ha descontado 1 crédito. Hemos procesado tu clase de las ' . $formattedTime . '. ¡Te esperamos!')
                    ->success()
                    ->duration(8000)
                    ->send();

            } else {
                // 3B. NO TIENE CRÉDITOS: Bloquear y avisar
                Notification::make()
                    ->title('Créditos Insuficientes')
                    ->body('Intentamos procesar tu reserva para las ' . $formattedTime . ', pero no tienes créditos activos. Por favor, adquiere un paquete para asegurar tu lugar.')
                    ->danger()
                    ->persistent()
                    ->send();

                session(['pending_appointment' => [
                    'tenant_id' => $booking['tenant_id'] ?? null,
                    'date' => $booking['date'] ?? null,
                    'time_slot' => $booking['time_slot'] ?? null,
                ]]);
                session()->forget('pending_booking');
                
                // Redirigimos directamente a la vista de compra
                return redirect('/comprar-creditos');
            }

            // 4. Borramos la memoria temporal para que no cicle
            session()->forget('pending_booking');
        }

        return $next($request);
    }
}

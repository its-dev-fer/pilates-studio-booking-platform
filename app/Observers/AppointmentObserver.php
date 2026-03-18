<?php

namespace App\Observers;

use App\Mail\AppointmentConfirmationMail;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        if ($appointment->user && $appointment->user->email) {
            Mail::to($appointment->user->email)
                ->send(new AppointmentConfirmationMail($appointment, false)); // false = No es admin
        }

        // 2. Enviar correo a los Administradores (Modo Admin)
        $adminEmails = User::role('admin')->pluck('email')->toArray();
        
        if (!empty($adminEmails)) {
            Mail::to($adminEmails)
                ->send(new AppointmentConfirmationMail($appointment, true)); // true = Sí es admin
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}

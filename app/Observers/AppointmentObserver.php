<?php

namespace App\Observers;

use App\Mail\AppointmentConfirmationMail;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $appointment->loadMissing(['user', 'tenant', 'creditPurchaseRequest.package']);

        if ($appointment->user && $appointment->user->email) {
            Mail::to($appointment->user->email)
                ->send(new AppointmentConfirmationMail($appointment));
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

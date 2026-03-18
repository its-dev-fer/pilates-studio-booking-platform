<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'app:send-appointment-reminders';
    protected $description = 'Envía recordatorios de citas 2h y 15m antes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // 1. Buscamos SOLO las citas programadas para el día de HOY
        // Y que no se les haya enviado alguno de los dos recordatorios
        $appointments = Appointment::whereDate('date', $now->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->where(function ($query) {
                $query->where('reminder_2h_sent', false)
                      ->orWhere('reminder_15m_sent', false);
            })
            ->with(['user', 'tenant']) // Optimizamos consultas
            ->get();

        foreach ($appointments as $appointment) {
            // Combinamos la fecha y hora de la cita en un solo objeto Carbon
            $appointmentTime = Carbon::parse($appointment->date->format('Y-m-d') . ' ' . $appointment->time_slot);
            
            // Si la cita ya pasó, la ignoramos (por seguridad)
            if ($now->greaterThanOrEqualTo($appointmentTime)) {
                continue;
            }

            // Calculamos cuántos minutos faltan
            $minutesUntil = $now->diffInMinutes($appointmentTime, false);

            // CASO A: Faltan 120 minutos (2 horas) o menos, pero más de 15 min.
            if ($minutesUntil <= 120 && $minutesUntil > 15 && !$appointment->reminder_2h_sent) {
                if ($appointment->user && $appointment->user->email) {
                    Mail::to($appointment->user->email)->send(new AppointmentReminderMail($appointment, '2h'));
                }
                // Marcamos la bandera para no volver a enviarlo
                $appointment->update(['reminder_2h_sent' => true]);
                $this->info("Recordatorio 2h enviado a cita ID: {$appointment->id}");
            }

            // CASO B: Faltan 15 minutos o menos.
            if ($minutesUntil <= 15 && $minutesUntil > 0 && !$appointment->reminder_15m_sent) {
                if ($appointment->user && $appointment->user->email) {
                    Mail::to($appointment->user->email)->send(new AppointmentReminderMail($appointment, '15m'));
                }
                // Marcamos la bandera
                $appointment->update(['reminder_15m_sent' => true]);
                $this->info("Recordatorio 15m enviado a cita ID: {$appointment->id}");
            }
        }
    }
}

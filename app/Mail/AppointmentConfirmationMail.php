<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function envelope(): Envelope
    {
        // Formateamos la fecha para el asunto del correo
        $fecha = Carbon::parse($this->appointment->date)->format('d/m/Y');
        $hora = Carbon::parse($this->appointment->time_slot)->format('h:i A');
        $adminEmails = User::role('admin')
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all();
        
        return new Envelope(
            subject: 'Confirmacion de cita - '.$fecha.' '.$hora,
            bcc: $adminEmails,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointments.confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

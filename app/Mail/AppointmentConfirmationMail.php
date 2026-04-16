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
        $methodLabel = match ($this->appointment->payment_method) {
            'transfer' => 'Transferencia bancaria',
            'cash' => 'Efectivo',
            'cash_at_arrival' => 'Efectivo al llegar',
            'stripe' => 'Tarjeta (Stripe)',
            'credit_balance' => 'Credito de paquete',
            default => 'No especificado',
        };

        $originLabel = match ($this->appointment->booking_origin) {
            'approved_credit_request' => 'Solicitud de credito aprobada',
            'stripe_checkout_pending_appointment' => 'Compra de creditos en linea (Stripe)',
            'landing_pending_booking' => 'Reserva desde landing con credito activo',
            'client_weekly_calendar' => 'Calendario semanal del cliente',
            'client_panel' => 'Panel del cliente',
            'admin_panel' => 'Panel administrativo',
            default => 'No especificado',
        };

        $creditRequest = $this->appointment->creditPurchaseRequest;

        return new Content(
            markdown: 'emails.appointments.confirmation',
            with: [
                'methodLabel' => $methodLabel,
                'originLabel' => $originLabel,
                'creditRequest' => $creditRequest,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

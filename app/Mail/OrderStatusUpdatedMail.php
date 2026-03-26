<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus,
        public string $newStatus,
        public bool $isGuestCheckout,
    ) {}

    public function envelope(): Envelope
    {
        $folio = str_pad((string) $this->order->id, 5, '0', STR_PAD_LEFT);

        $subjectLead = match ($this->newStatus) {
            'creado' => 'Actualización de pedido',
            'pagado' => 'Pago registrado',
            'empacado' => 'Pedido en preparación',
            'entregado' => 'Pedido entregado / listo',
            'cancelado' => 'Pedido cancelado',
            default => 'Actualización de pedido',
        };

        return new Envelope(
            subject: $subjectLead.' #'.$folio.' — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-updated',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

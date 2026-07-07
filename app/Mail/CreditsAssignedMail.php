<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreditsAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, int>  $affectedTenantIds
     */
    public function __construct(
        public User $user,
        public string $source,
        public array $affectedTenantIds = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tus créditos han sido abonados - '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.credits.assigned',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

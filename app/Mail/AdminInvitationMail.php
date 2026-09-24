<?php

namespace App\Mail;

use App\Models\AdminInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $acceptUrl;

    public function __construct(
        public AdminInvitation $invitation,
        public string $plainToken,
    ) {
        $this->acceptUrl = route('admin.invitations.accept', ['token' => $this->plainToken]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Akses Administrator — PEMIRA 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-invitation',
            text: 'emails.admin-invitation-text',
            with: [
                'invitation' => $this->invitation,
                'acceptUrl' => $this->acceptUrl,
                'inviterName' => $this->invitation->inviter?->getAdminDisplayName() ?? 'Super Administrator',
                'expiresAtFormatted' => $this->invitation->expires_at->format('d/m/Y H:i').' WITA',
            ],
        );
    }
}

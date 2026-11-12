<?php
namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class AccountRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct (public User $user) 
    {
        $this->user->loadMissing(['operatingUnit','roles']);
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'BPI-IPMS Account Request Received - Pending PMO Verification',
        );
    }
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.account-request-submitted',
            with: [
                'roleLabel' => $this->user->roleLabel(),
                'unitLabel' => $this->user->operatingUnitLabel(),
                'landingUrl' => route('landing'),
            ]
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

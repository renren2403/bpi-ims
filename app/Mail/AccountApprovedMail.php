<?php

namespace App\Mail;

use App\Models\OperatingUnit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     */
   public function __construct(public User $user)
   {
    $this->user->loadMissing(['operatingUnit','roles']);
   }

   public function envelope(): Envelope
   {
    return new Envelope(
        subject: 'BPI-IPMS Account Request Approved',
    );
   }

   public function content(): Content
   {
    return new Content(
        markdown:'mail.account-approved',
        with: [
            'roleLabel' => $this->user->roleLabel(),
            'unitLabel' => $this->user->operatingUnitLabel(),
            'loginUrl' => route('login'),
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

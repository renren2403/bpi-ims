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

class NewAccountRequestAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $applicant)
    {
        $this->applicant->loadMissing(['operatingUnit', 'roles']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[BPI-IPMS Action Required] New Account Request - {$this->applicant->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-account-request-alert',
            with: [
                'applicantName' => $this->applicant->name,
                'applicantEmail' => $this->applicant->email,
                'designation' => $this->applicant->designation ?? 'Not specified',
                'roleLabel' => $this->applicant->roleLabel(),
                'unitLabel' => $this->applicant->operatingUnitLabel(),
                'justification' => $this->applicant->approval_remarks ? str_replace('Justification: ', '', $this->applicant->approval_remarks) : 'None provided',
                'reviewUrl' => route('admin.users.index', ['approval_status' => 'pending']),
                'submittedAt' => now()->format('F j, Y g:i A'),
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

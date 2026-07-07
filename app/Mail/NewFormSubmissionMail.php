<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Customer;
use App\Services\SubmissionData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

final class NewFormSubmissionMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public RegistrationForm $form,
        public ?FormSubmission $submission,
        public SubmissionData $data,
        public ?Customer $customer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New form submission').': '.$this->form->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.form-submission',
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}

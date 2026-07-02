<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class QuoteEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Quote $quote,
        public string $pdfPath,
        public ?EmailTemplate $emailTemplate = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->emailTemplate instanceof EmailTemplate
            ? $this->replaceVariables($this->emailTemplate->subject)
            : 'Árajánlat / Quote '.$this->quote->quote_number;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        if ($this->emailTemplate instanceof EmailTemplate) {
            return new Content(htmlString: $this->replaceVariables($this->emailTemplate->body));
        }

        return new Content(view: 'emails.quote');
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->quote->quote_number.'.pdf')
                ->withMime('application/pdf'),
        ];
    }

    private function replaceVariables(string $content): string
    {
        $replacements = [
            '{quote_number}' => $this->quote->quote_number,
            '{customer_name}' => $this->quote->customer?->name ?? '',
            '{total}' => number_format((float) $this->quote->total, 0, ',', ' ').' Ft',
            '{valid_until}' => $this->quote->valid_until?->format('Y-m-d') ?? '',
            '{view_url}' => $this->quote->view_token
                ? route('quotes.public-view', $this->quote->view_token)
                : '',
            '{date}' => now()->format('Y-m-d'),
        ];

        return strtr($content, $replacements);
    }
}

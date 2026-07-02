<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\QuoteStatus;
use App\Mail\QuoteEmail;
use App\Models\EmailTemplate;
use App\Models\Quote;
use App\Models\QuoteTemplate;
use App\Models\QuoteVersion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class QuoteTemplateService
{
    /**
     * Generate PDF for a quote using a Blade template stored in the DB.
     */
    public function generatePdf(Quote $quote, ?QuoteTemplate $template = null): string
    {
        $quote->load(['customer.addresses', 'items.product']);

        $template ??= $this->getDefaultTemplate($quote->team_id);

        throw_unless($template, InvalidArgumentException::class, 'No template specified and no default template found.');

        $html = Blade::render($template->body, [
            'quote' => $quote,
            'customer' => $quote->customer,
            'items' => $quote->items,
        ]);

        $pdf = Pdf::loadHTML($html);

        $filename = 'quotes/'.$quote->quote_number.'.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        return Storage::disk('local')->path($filename);
    }

    /**
     * Create a version snapshot of the current quote state.
     */
    public function createVersion(
        Quote $quote,
        ?QuoteTemplate $template = null,
        ?string $pdfPath = null,
    ): QuoteVersion {
        $quote->load(['items']);

        $snapshot = [
            'quote' => $quote->toArray(),
            'items' => $quote->items->toArray(),
        ];

        $previousVersion = $quote->versions()->orderByDesc('version_number')->first();

        $changesLog = $previousVersion
            ? $this->calculateChanges($previousVersion->snapshot, $snapshot)
            : ['initial' => true];

        $versionNumber = ($previousVersion?->version_number ?? 0) + 1;

        return DB::transaction(fn (): QuoteVersion => QuoteVersion::query()->create([
            'team_id' => $quote->team_id,
            'quote_id' => $quote->id,
            'quote_template_id' => $template?->id,
            'version_number' => $versionNumber,
            'snapshot' => $snapshot,
            'changes_log' => $changesLog,
            'pdf_path' => $pdfPath,
            'created_by' => Auth::id(),
        ]));
    }

    /**
     * Send the quote via email with the PDF attached.
     * Updates status to Sent and creates a version.
     */
    public function sendQuote(
        Quote $quote,
        string $recipientEmail,
        string $recipientName,
        ?QuoteTemplate $template = null,
        ?EmailTemplate $emailTemplate = null,
    ): void {
        DB::transaction(function () use ($quote, $recipientEmail, $recipientName, $template, $emailTemplate): void {
            $pdfPath = $this->generatePdf($quote, $template);

            $this->createVersion($quote, $template, $pdfPath);

            if (! $quote->view_token) {
                $quote->update(['view_token' => Str::uuid()->toString()]);
            }

            Mail::to($recipientEmail, $recipientName)
                ->send(new QuoteEmail(
                    quote: $quote->refresh(),
                    pdfPath: $pdfPath,
                    emailTemplate: $emailTemplate,
                ));

            $quote->update([
                'status' => QuoteStatus::Sent,
                'sent_at' => now(),
            ]);
        });
    }

    /**
     * Mark a quote as viewed (triggered by public token URL access).
     */
    public function markAsViewed(Quote $quote): void
    {
        if ($quote->status === QuoteStatus::Sent) {
            $quote->update([
                'status' => QuoteStatus::Viewed,
                'viewed_at' => now(),
            ]);
        }
    }

    /**
     * Get the default active template for a team.
     */
    public function getDefaultTemplate(?int $teamId): ?QuoteTemplate
    {
        return QuoteTemplate::query()
            ->where('team_id', $teamId)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Calculate changes between two version snapshots.
     *
     * @param  array<string, mixed>  $oldSnapshot
     * @param  array<string, mixed>  $newSnapshot
     * @return array<string, mixed>
     */
    private function calculateChanges(array $oldSnapshot, array $newSnapshot): array
    {
        $changes = [];

        $oldQuote = $oldSnapshot['quote'] ?? [];
        $newQuote = $newSnapshot['quote'] ?? [];

        foreach ($newQuote as $key => $value) {
            if (($oldQuote[$key] ?? null) !== $value) {
                $changes['quote'][$key] = [
                    'old' => $oldQuote[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        $oldItemCount = count($oldSnapshot['items'] ?? []);
        $newItemCount = count($newSnapshot['items'] ?? []);

        if ($oldItemCount !== $newItemCount) {
            $changes['items_count'] = [
                'old' => $oldItemCount,
                'new' => $newItemCount,
            ];
        }

        return $changes;
    }
}

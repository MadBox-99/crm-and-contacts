<?php

declare(strict_types=1);

use App\Enums\QuoteStatus;
use App\Mail\QuoteEmail;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\QuoteTemplate;
use App\Models\Team;
use App\Services\QuoteTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = resolve(QuoteTemplateService::class);
    $this->customer = Customer::factory()->create();
    $this->template = QuoteTemplate::factory()->default()->create();
    $this->quote = Quote::factory()->draft()->create([
        'customer_id' => $this->customer->id,
        'team_id' => $this->template->team_id,
    ]);
    QuoteItem::factory()->count(2)->create([
        'quote_id' => $this->quote->id,
        'team_id' => $this->quote->team_id,
    ]);
});

it('generates a PDF from a quote with default template', function (): void {
    $path = $this->service->generatePdf($this->quote);

    expect($path)->toBeString()
        ->and(file_exists($path))->toBeTrue();

    @unlink($path);
});

it('generates a PDF from a quote with a specific template', function (): void {
    $specificTemplate = QuoteTemplate::factory()->create([
        'team_id' => $this->quote->team_id,
    ]);

    $path = $this->service->generatePdf($this->quote, $specificTemplate);

    expect($path)->toBeString()
        ->and(file_exists($path))->toBeTrue();

    @unlink($path);
});

it('throws exception when no default template and none specified', function (): void {
    $teamWithNoTemplate = Team::factory()->create();
    $quote = Quote::factory()->draft()->create([
        'customer_id' => $this->customer->id,
        'team_id' => $teamWithNoTemplate->id,
    ]);

    $this->service->generatePdf($quote);
})->throws(InvalidArgumentException::class, 'No template specified and no default template found.');

it('creates a version with correct snapshot data', function (): void {
    $version = $this->service->createVersion($this->quote, $this->template);

    expect($version->quote_id)->toBe($this->quote->id)
        ->and($version->version_number)->toBe(1)
        ->and($version->snapshot)->toBeArray()
        ->and($version->snapshot)->toHaveKeys(['quote', 'items'])
        ->and($version->snapshot['items'])->toHaveCount(2)
        ->and($version->changes_log)->toBe(['initial' => true]);
});

it('increments version number correctly', function (): void {
    $v1 = $this->service->createVersion($this->quote);
    $this->quote->update(['notes' => 'Changed notes']);
    $v2 = $this->service->createVersion($this->quote);

    expect($v1->version_number)->toBe(1)
        ->and($v2->version_number)->toBe(2);
});

it('calculates changes between versions', function (): void {
    $this->service->createVersion($this->quote);

    $this->quote->update(['notes' => 'Updated notes']);
    $v2 = $this->service->createVersion($this->quote);

    expect($v2->changes_log)->toBeArray()
        ->and($v2->changes_log)->toHaveKey('quote');
});

it('sends a quote email with PDF attachment', function (): void {
    Mail::fake();

    $this->service->sendQuote(
        $this->quote,
        'customer@example.com',
        'Test Customer',
        $this->template,
    );

    Mail::assertQueued(QuoteEmail::class, fn (QuoteEmail $mail): bool => $mail->hasTo('customer@example.com')
        && $mail->quote->id === $this->quote->id);
});

it('updates status to Sent when sending', function (): void {
    Mail::fake();

    $this->service->sendQuote(
        $this->quote,
        'customer@example.com',
        'Test Customer',
        $this->template,
    );

    $this->quote->refresh();

    expect($this->quote->status)->toBe(QuoteStatus::Sent)
        ->and($this->quote->sent_at)->not->toBeNull();
});

it('generates a view token when sending', function (): void {
    Mail::fake();

    expect($this->quote->view_token)->toBeNull();

    $this->service->sendQuote(
        $this->quote,
        'customer@example.com',
        'Test Customer',
        $this->template,
    );

    $this->quote->refresh();

    expect($this->quote->view_token)->not->toBeNull();
});

it('does not overwrite existing view token when sending again', function (): void {
    Mail::fake();

    $this->quote->update(['view_token' => 'existing-token']);

    $this->service->sendQuote(
        $this->quote,
        'customer@example.com',
        'Test Customer',
        $this->template,
    );

    $this->quote->refresh();

    expect($this->quote->view_token)->toBe('existing-token');
});

it('marks quote as viewed when status is Sent', function (): void {
    $quote = Quote::factory()->sent()->create([
        'customer_id' => $this->customer->id,
    ]);

    $this->service->markAsViewed($quote);
    $quote->refresh();

    expect($quote->status)->toBe(QuoteStatus::Viewed)
        ->and($quote->viewed_at)->not->toBeNull();
});

it('does not change status when marking viewed if not in Sent state', function (): void {
    $this->service->markAsViewed($this->quote);
    $this->quote->refresh();

    expect($this->quote->status)->toBe(QuoteStatus::Draft);
});

it('returns the default template for a team', function (): void {
    $result = $this->service->getDefaultTemplate($this->template->team_id);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($this->template->id);
});

it('returns null when no default template exists', function (): void {
    $result = $this->service->getDefaultTemplate(999);

    expect($result)->toBeNull();
});

<?php

declare(strict_types=1);

use App\Enums\QuoteStatus;
use App\Events\QuoteStatusChanged;
use App\Models\Quote;
use Illuminate\Support\Facades\Event;

it('dispatches QuoteStatusChanged event when status changes', function (): void {
    $quote = Quote::factory()->create(['status' => QuoteStatus::Draft]);

    Event::fake([QuoteStatusChanged::class]);

    $quote->update(['status' => QuoteStatus::Sent]);

    Event::assertDispatched(QuoteStatusChanged::class, fn (QuoteStatusChanged $event): bool => $event->quote->id === $quote->id
        && $event->previousStatus === QuoteStatus::Draft->value);
});

it('does not dispatch QuoteStatusChanged when other fields change', function (): void {
    $quote = Quote::factory()->create();

    Event::fake([QuoteStatusChanged::class]);

    $quote->update(['notes' => 'Updated notes']);

    Event::assertNotDispatched(QuoteStatusChanged::class);
});

<?php

declare(strict_types=1);

use App\Enums\OpportunityStage;
use App\Events\OpportunityStageMoved;
use App\Models\Opportunity;
use Illuminate\Support\Facades\Event;

it('dispatches OpportunityStageMoved event when stage changes', function (): void {
    $opportunity = Opportunity::factory()->create(['stage' => OpportunityStage::Lead]);

    Event::fake([OpportunityStageMoved::class]);

    $opportunity->update(['stage' => OpportunityStage::Qualified]);

    Event::assertDispatched(OpportunityStageMoved::class, fn (OpportunityStageMoved $event): bool => $event->opportunity->id === $opportunity->id
        && $event->previousStage === OpportunityStage::Lead->value);
});

it('does not dispatch OpportunityStageMoved when other fields change', function (): void {
    $opportunity = Opportunity::factory()->create();

    Event::fake([OpportunityStageMoved::class]);

    $opportunity->update(['title' => 'Updated title']);

    Event::assertNotDispatched(OpportunityStageMoved::class);
});

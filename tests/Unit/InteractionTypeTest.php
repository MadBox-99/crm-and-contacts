<?php

declare(strict_types=1);

use App\Enums\InteractionType;

it('has a form submission case with a label', function (): void {
    expect(InteractionType::FormSubmission->value)->toBe('form_submission')
        ->and(InteractionType::FormSubmission->getLabel())->not->toBeNull();
});

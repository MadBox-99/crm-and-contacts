<?php

declare(strict_types=1);

use App\Ai\Agents\CrmAssistant;
use App\Ai\Tools\AggregateModel;
use App\Ai\Tools\GetModelDetails;
use App\Ai\Tools\ListModels;
use App\Ai\Tools\QueryModel;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Enums\Lab;

it('has gemini provider attribute', function (): void {
    $reflection = new ReflectionClass(CrmAssistant::class);
    $attributes = $reflection->getAttributes(Provider::class);

    expect($attributes)->toHaveCount(1)
        ->and($attributes[0]->newInstance()->value)->toBe(Lab::Gemini);
});

it('has gemini-2.5-flash model attribute', function (): void {
    $reflection = new ReflectionClass(CrmAssistant::class);
    $attributes = $reflection->getAttributes(Model::class);

    expect($attributes)->toHaveCount(1)
        ->and($attributes[0]->newInstance()->value)->toBe('gemini-2.5-flash');
});

it('has correct configuration attributes', function (): void {
    $reflection = new ReflectionClass(CrmAssistant::class);

    expect($reflection->getAttributes(MaxSteps::class))->toHaveCount(1)
        ->and($reflection->getAttributes(MaxTokens::class))->toHaveCount(1)
        ->and($reflection->getAttributes(Temperature::class))->toHaveCount(1)
        ->and($reflection->getAttributes(Timeout::class))->toHaveCount(1);
});

it('has correct instructions', function (): void {
    $agent = new CrmAssistant();
    $instructions = $agent->instructions();

    expect($instructions)
        ->toContain('CRM assistant')
        ->toContain('READ-ONLY');
});

it('provides all required tools', function (): void {
    $agent = new CrmAssistant();
    $tools = collect($agent->tools())->map(fn ($tool): string => $tool::class)->all();

    expect($tools)->toContain(
        ListModels::class,
        QueryModel::class,
        GetModelDetails::class,
        AggregateModel::class,
    );
});

it('can be faked and prompted', function (): void {
    CrmAssistant::fake([
        'You have 42 customers in total.',
    ]);

    $response = new CrmAssistant()->prompt('How many customers do I have?');

    expect((string) $response)->toContain('42 customers');

    CrmAssistant::assertPrompted('How many customers do I have?');
});

<?php

declare(strict_types=1);

use App\Filament\Imports\OpportunityImporter;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Spatie\Permission\Models\Permission;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    Permission::query()->firstOrCreate(['name' => 'view_any_opportunity']);
    $this->user->givePermissionTo('view_any_opportunity');

    $this->actingAs($this->user);
});

it('has importer class configured correctly', function (): void {
    expect(OpportunityImporter::class)->toBeString();
    expect(OpportunityImporter::getColumns())->toBeArray()->not()->toBeEmpty();
});

it('has correct column mappings', function (): void {
    $columns = OpportunityImporter::getColumns();
    $columnNames = array_map(fn (ImportColumn $column): string => $column->getName(), $columns);

    // Customer fields
    expect($columnNames)->toContain('customer_unique_identifier')
        ->and($columnNames)->toContain('customer_name')
        ->and($columnNames)->toContain('customer_type')
        ->and($columnNames)->toContain('customer_tax_number')
        ->and($columnNames)->toContain('customer_registration_number')
        ->and($columnNames)->toContain('customer_email')
        ->and($columnNames)->toContain('customer_phone')
        ->and($columnNames)->toContain('source')
        // Opportunity fields
        ->and($columnNames)->toContain('title')
        ->and($columnNames)->toContain('probability')
        ->and($columnNames)->toContain('stage')
        ->and($columnNames)->toContain('description')
        ->and($columnNames)->toContain('value')
        ->and($columnNames)->toContain('expected_close_date')
        ->and($columnNames)->toContain('assigned_to');
});

it('has required customer fields configured', function (): void {
    $columns = OpportunityImporter::getColumns();

    $customerUniqueIdColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'customer_unique_identifier');
    $customerNameColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'customer_name');
    $customerTypeColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'customer_type');
    $customerEmailColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'customer_email');
    $sourceColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'source');

    expect($customerUniqueIdColumn)->not()->toBeNull();
    expect($customerNameColumn)->not()->toBeNull();
    expect($customerTypeColumn)->not()->toBeNull();
    expect($customerEmailColumn)->not()->toBeNull();
    expect($sourceColumn)->not()->toBeNull();
});

it('has required opportunity fields configured', function (): void {
    $columns = OpportunityImporter::getColumns();

    $titleColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'title');
    $probabilityColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'probability');
    $stageColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'stage');

    expect($titleColumn)->not()->toBeNull();
    expect($probabilityColumn)->not()->toBeNull();
    expect($stageColumn)->not()->toBeNull();
});

it('has customer unique identifier column with correct label', function (): void {
    $columns = OpportunityImporter::getColumns();
    $customerUniqueIdColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'customer_unique_identifier');

    expect($customerUniqueIdColumn)->not()->toBeNull();
    expect($customerUniqueIdColumn->getLabel())->toBe('Unique Identifier');
});

it('has customer email column with correct label', function (): void {
    $columns = OpportunityImporter::getColumns();
    $customerEmailColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'customer_email');

    expect($customerEmailColumn)->not()->toBeNull();
    expect($customerEmailColumn->getLabel())->toBe('Email Address');
});

it('has probability column configured as numeric', function (): void {
    $columns = OpportunityImporter::getColumns();
    $probabilityColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'probability');

    expect($probabilityColumn)->not()->toBeNull();

    $reflection = new ReflectionClass($probabilityColumn);
    $property = $reflection->getProperty('isNumeric');

    expect($property->getValue($probabilityColumn))->toBeTrue();
});

it('has stage column configured with example', function (): void {
    $columns = OpportunityImporter::getColumns();
    $stageColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'stage');

    expect($stageColumn)->not()->toBeNull();
});

it('has assigned_to column configured with correct label', function (): void {
    $columns = OpportunityImporter::getColumns();
    $assignedToColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'assigned_to');

    expect($assignedToColumn)->not()->toBeNull();
    expect($assignedToColumn->getLabel())->toBe('Assigned User Email');
});

it('has correct model configured', function (): void {
    $reflection = new ReflectionClass(OpportunityImporter::class);
    $property = $reflection->getProperty('model');

    $model = $property->getValue();

    expect($model)->toBe(Opportunity::class);
});

it('has source column with correct label and validation', function (): void {
    $columns = OpportunityImporter::getColumns();
    $sourceColumn = collect($columns)->first(fn ($col): bool => $col->getName() === 'source');

    expect($sourceColumn)->not()->toBeNull();
    expect($sourceColumn->getLabel())->toBe('Source');
});

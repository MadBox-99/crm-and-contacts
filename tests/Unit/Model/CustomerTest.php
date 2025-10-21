<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\Communication;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAttribute;
use App\Models\CustomerContact;
use App\Models\Interaction;
use App\Models\Invoice;
use App\Models\Opportunity;
use App\Models\Order;
use App\Models\Quote;
use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $customer = Customer::factory()->create();

    expect($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->name)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create([
        'name' => 'Acme Corp',
        'email' => 'info@acme.com',
        'phone' => '+36301234567',
        'is_active' => true,
    ]);

    expect($customer->name)->toBe('Acme Corp')
        ->and($customer->email)->toBe('info@acme.com')
        ->and($customer->phone)->toBe('+36301234567')
        ->and($customer->is_active)->toBeTrue();
});

it('casts is_active to boolean', function (): void {
    $customer = Customer::factory()->create(['is_active' => 1]);

    expect($customer->is_active)->toBeTrue()
        ->and($customer->is_active)->toBeBool();
});

it('uses soft deletes', function (): void {
    $customer = Customer::factory()->create();
    $customer->delete();

    expect($customer->trashed())->toBeTrue();
});

it('has contacts relationship', function (): void {
    $customer = Customer::factory()->create();
    $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);

    expect($customer->contacts)->toHaveCount(1)
        ->and($customer->contacts->first()->id)->toBe($contact->id);
});

it('has addresses relationship', function (): void {
    $customer = Customer::factory()->create();
    $address = CustomerAddress::factory()->create(['customer_id' => $customer->id]);

    expect($customer->addresses)->toHaveCount(1)
        ->and($customer->addresses->first()->id)->toBe($address->id);
});

it('has attributes relationship', function (): void {
    $customer = Customer::factory()->create();
    $attribute = CustomerAttribute::factory()->create(['customer_id' => $customer->id]);

    expect($customer->attributes)->toHaveCount(1)
        ->and($customer->attributes->first()->id)->toBe($attribute->id);
});

it('has campaigns relationship', function (): void {
    $customer = Customer::factory()->create();
    $campaign = Campaign::factory()->create();

    expect($customer->campaigns())->toBeInstanceOf(HasMany::class);
});

it('has opportunities relationship', function (): void {
    $customer = Customer::factory()->create();
    $opportunity = Opportunity::factory()->create(['customer_id' => $customer->id]);

    expect($customer->opportunities)->toHaveCount(1)
        ->and($customer->opportunities->first()->id)->toBe($opportunity->id);
});

it('has quotes relationship', function (): void {
    $customer = Customer::factory()->create();
    $quote = Quote::factory()->create(['customer_id' => $customer->id]);

    expect($customer->quotes)->toHaveCount(1)
        ->and($customer->quotes->first()->id)->toBe($quote->id);
});

it('has orders relationship', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    expect($customer->orders)->toHaveCount(1)
        ->and($customer->orders->first()->id)->toBe($order->id);
});

it('has invoices relationship', function (): void {
    $customer = Customer::factory()->create();
    $invoice = Invoice::factory()->create(['customer_id' => $customer->id]);

    expect($customer->invoices)->toHaveCount(1)
        ->and($customer->invoices->first()->id)->toBe($invoice->id);
});

it('has interactions relationship', function (): void {
    $customer = Customer::factory()->create();
    $interaction = Interaction::factory()->create(['customer_id' => $customer->id]);

    expect($customer->interactions)->toHaveCount(1)
        ->and($customer->interactions->first()->id)->toBe($interaction->id);
});

it('has tasks relationship', function (): void {
    $customer = Customer::factory()->create();
    $task = Task::factory()->create(['customer_id' => $customer->id]);

    expect($customer->tasks)->toHaveCount(1)
        ->and($customer->tasks->first()->id)->toBe($task->id);
});

it('has complaints relationship', function (): void {
    $customer = Customer::factory()->create();
    $complaint = Complaint::factory()->create(['customer_id' => $customer->id]);

    expect($customer->complaints)->toHaveCount(1)
        ->and($customer->complaints->first()->id)->toBe($complaint->id);
});

it('has communications relationship', function (): void {
    $customer = Customer::factory()->create();
    $communication = Communication::factory()->create(['customer_id' => $customer->id]);

    expect($customer->communications)->toHaveCount(1)
        ->and($customer->communications->first()->id)->toBe($communication->id);
});

<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Campaign;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Interaction;
use App\Models\Invoice;
use App\Models\LoyaltyLevel;
use App\Models\Opportunity;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Shipment;
use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

final class ListModels implements Tool
{
    /**
     * @var array<string, array{model: class-string, description: string, relationships: list<string>}>
     */
    public const array AVAILABLE_MODELS = [
        'customers' => [
            'model' => Customer::class,
            'description' => 'CRM customers with contact info, loyalty data, and business details',
            'relationships' => ['contacts', 'addresses', 'orders', 'invoices', 'opportunities', 'complaints', 'loyaltyLevel'],
        ],
        'orders' => [
            'model' => Order::class,
            'description' => 'Sales orders linked to customers with items and shipping',
            'relationships' => ['customer', 'orderItems', 'invoices', 'shipments'],
        ],
        'invoices' => [
            'model' => Invoice::class,
            'description' => 'Invoices generated from orders with payment tracking',
            'relationships' => ['customer', 'order', 'invoiceItems'],
        ],
        'products' => [
            'model' => Product::class,
            'description' => 'Product catalog with pricing and categories',
            'relationships' => ['category'],
        ],
        'opportunities' => [
            'model' => Opportunity::class,
            'description' => 'Sales pipeline opportunities with stages and probability',
            'relationships' => ['customer', 'campaign', 'assignedUser', 'quotes'],
        ],
        'complaints' => [
            'model' => Complaint::class,
            'description' => 'Customer complaints and issue tracking',
            'relationships' => ['customer'],
        ],
        'campaigns' => [
            'model' => Campaign::class,
            'description' => 'Marketing campaigns targeting customers',
            'relationships' => ['customers'],
        ],
        'quotes' => [
            'model' => Quote::class,
            'description' => 'Sales quotes linked to opportunities and customers',
            'relationships' => ['customer', 'opportunity'],
        ],
        'shipments' => [
            'model' => Shipment::class,
            'description' => 'Shipment tracking for orders',
            'relationships' => ['order', 'customer', 'carrier'],
        ],
        'loyalty_levels' => [
            'model' => LoyaltyLevel::class,
            'description' => 'Loyalty program tiers and benefits',
            'relationships' => [],
        ],
        'interactions' => [
            'model' => Interaction::class,
            'description' => 'Customer interaction history (calls, meetings, emails)',
            'relationships' => ['customer'],
        ],
        'tasks' => [
            'model' => Task::class,
            'description' => 'Tasks assigned to team members',
            'relationships' => ['customer'],
        ],
    ];

    public function description(): string
    {
        return 'List all available database models and their descriptions. Use this to understand what data is available before querying.';
    }

    public function handle(Request $request): string
    {
        $result = [];

        foreach (self::AVAILABLE_MODELS as $key => $config) {
            $result[] = [
                'key' => $key,
                'description' => $config['description'],
                'available_relationships' => $config['relationships'],
            ];
        }

        return json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}

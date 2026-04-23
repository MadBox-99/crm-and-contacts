<?php

declare(strict_types=1);

use Madbox99\FilamentWooCommerce\Sync\CategorySyncer;
use Madbox99\FilamentWooCommerce\Sync\CustomerSyncer;
use Madbox99\FilamentWooCommerce\Sync\OrderSyncer;
use Madbox99\FilamentWooCommerce\Sync\ProductSyncer;

return [
    /*
    |--------------------------------------------------------------------------
    | Entity Mappings
    |--------------------------------------------------------------------------
    |
    | Each entity the plugin can sync maps to one local Eloquent model in the
    | host application. Override `model` with your own model class and tweak
    | `field_map` to translate WooCommerce payload keys to your columns.
    |
    | Setting `enabled` to false skips the entity entirely (no UI, no jobs).
    |
    */

    'mappings' => [
        'products' => [
            'enabled' => true,
            'model' => App\Models\Product::class,
            'syncer' => ProductSyncer::class,
            'field_map' => [
                'name' => 'name',
                'sku' => 'sku',
                'description' => 'description',
                'regular_price' => 'unit_price',
            ],
        ],

        'product_categories' => [
            'enabled' => true,
            'model' => App\Models\ProductCategory::class,
            'syncer' => CategorySyncer::class,
            'field_map' => [
                'name' => 'name',
                'description' => 'description',
            ],
        ],

        'customers' => [
            'enabled' => true,
            'model' => App\Models\Customer::class,
            'syncer' => CustomerSyncer::class,
            'field_map' => [
                'email' => 'email',
                'phone' => 'phone',
            ],
        ],

        'orders' => [
            'enabled' => true,
            'model' => App\Models\Order::class,
            'syncer' => OrderSyncer::class,
            'field_map' => [
                'number' => 'order_number',
                'status' => 'status',
                'total' => 'total',
                'date_created' => 'order_date',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    */

    'queue' => [
        'connection' => env('WOO_QUEUE_CONNECTION'),
        'name' => env('WOO_QUEUE_NAME', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-tenant support
    |--------------------------------------------------------------------------
    |
    | The CRM scopes every synced model by team_id via BelongsToTeam. Each
    | WooStore is assigned a team in the UI; the syncer auto-populates
    | team_id on newly created records and bypasses global scopes when
    | resolving existing mappings — so queue jobs work without an
    | authenticated tenant context.
    |
    */

    'tenant' => [
        'column' => env('WOO_TENANT_COLUMN', 'team_id'),
        'model' => App\Models\Team::class,
        'label_column' => 'name',
        'bypass_global_scopes' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduler
    |--------------------------------------------------------------------------
    |
    | When enabled, the plugin registers a scheduled job that syncs every
    | active store at the given cron expression. Disable if you prefer to
    | trigger sync manually or via webhooks only.
    |
    */

    'schedule' => [
        'enabled' => env('WOO_SCHEDULE_ENABLED', true),
        'cron' => env('WOO_SCHEDULE_CRON', '0 * * * *'), // hourly
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */

    'http' => [
        'timeout' => 30,
        'per_page' => 100,
        'verify_ssl' => env('WOO_VERIFY_SSL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    */

    'webhooks' => [
        'enabled' => env('WOO_WEBHOOKS_ENABLED', false),
        'secret_header' => 'x-wc-webhook-signature',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Panel
    |--------------------------------------------------------------------------
    */

    'filament' => [
        'navigation_group' => 'WooCommerce',
        'navigation_sort' => 90,
        'cluster' => null,
    ],
];

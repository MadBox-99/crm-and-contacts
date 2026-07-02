<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Team;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteTemplate;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\InvoiceService;
use App\Services\ShipmentService;
use App\Services\ChatService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * @property Team $team
 * @property User $user
 * @property Customer $customer
 * @property Product $product
 * @property Quote $quote
 * @property QuoteTemplate $template
 * @property Order $order
 * @property string $token
 * @property string $pdfPath
 * @property object $service
 * @property OrderService $orderService
 * @property InvoiceService $invoiceService
 * @property ShipmentService $shipmentService
 * @property ChatService $chatService
 * @property PricingService $pricingService
 */
abstract class TestCase extends BaseTestCase
{
    //
}

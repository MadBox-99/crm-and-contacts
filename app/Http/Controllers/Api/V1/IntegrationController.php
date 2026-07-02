<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\SalesIntegrationInterface;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class IntegrationController extends Controller
{
    public function __construct(
        private readonly SalesIntegrationInterface $integration,
    ) {}

    public function pushOrder(Order $order): JsonResponse
    {
        $result = $this->integration->pushOrderToAccounting($order);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function pushInvoice(Invoice $invoice): JsonResponse
    {
        $result = $this->integration->pushInvoiceToFinance($invoice);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function checkInventory(Product $product, Request $request): JsonResponse
    {
        $quantity = $request->integer('quantity', 1);
        $result = $this->integration->checkInventory($product, $quantity);

        return response()->json($result);
    }

    public function reserveStock(Order $order, Request $request): JsonResponse
    {
        $items = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $result = $this->integration->reserveStock($order, $items['items']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}

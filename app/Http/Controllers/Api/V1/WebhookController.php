<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class WebhookController extends Controller
{
    /**
     * Receive inventory change webhooks from external systems.
     */
    public function inventoryChanged(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event' => ['required', 'string'],
            'product_sku' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
            'warehouse' => ['nullable', 'string'],
            'timestamp' => ['nullable', 'string'],
        ]);

        Log::info('Webhook: Inventory change received', $validated);

        $product = Product::query()->where('sku', $validated['product_sku'])->first();

        if (! $product) {
            return response()->json([
                'status' => 'ignored',
                'message' => 'Product not found for SKU: '.$validated['product_sku'],
            ], 200);
        }

        // Log activity for the product inventory change
        activity()
            ->performedOn($product)
            ->withProperties([
                'event' => $validated['event'],
                'quantity' => $validated['quantity'],
                'warehouse' => $validated['warehouse'] ?? null,
            ])
            ->event('inventory_changed')
            ->log(sprintf('Inventory %s: %s units for %s', $validated['event'], $validated['quantity'], $product->name));

        return response()->json([
            'status' => 'processed',
            'product_id' => $product->id,
            'message' => 'Inventory change processed',
        ]);
    }
}

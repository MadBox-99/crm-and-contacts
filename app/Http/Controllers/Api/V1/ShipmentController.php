<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreShipmentRequest;
use App\Models\Order;
use App\Models\Shipment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class ShipmentController extends Controller
{
    public function store(StoreShipmentRequest $request): JsonResponse
    {
        try {
            $shipment = DB::transaction(function () use ($request) {
                // Find order by order_number if provided
                $order = null;
                if ($request->filled('order_number')) {
                    $order = Order::where('order_number', $request->order_number)->first();
                }

                // Generate unique shipment number
                $shipmentNumber = $this->generateShipmentNumber();

                // Create shipment
                $shipment = Shipment::create([
                    'customer_id' => $order?->customer_id,
                    'order_id' => $order?->id,
                    'external_customer_id' => $request->external_customer_id,
                    'external_order_id' => $request->external_order_id,
                    'shipment_number' => $shipmentNumber,
                    'carrier' => $request->carrier,
                    'tracking_number' => $request->tracking_number,
                    'status' => $request->status ?? ShipmentStatus::Pending->value,
                    'shipping_address' => $request->shipping_address,
                    'shipped_at' => $request->shipped_at,
                    'estimated_delivery_at' => $request->estimated_delivery_at,
                    'delivered_at' => $request->delivered_at,
                    'notes' => $request->notes,
                ]);

                // Create shipment items
                if ($request->filled('items')) {
                    foreach ($request->items as $itemData) {
                        $shipment->items()->create($itemData);
                    }
                }

                return $shipment->load('items');
            });

            return response()->json([
                'message' => 'Shipment created successfully',
                'data' => $shipment,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create shipment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $trackingNumber): JsonResponse
    {
        try {
            $shipment = Shipment::where('tracking_number', $trackingNumber)
                ->with(['customer', 'order', 'items', 'trackingEvents'])
                ->firstOrFail();

            return response()->json([
                'data' => $shipment,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Shipment not found',
            ], 404);
        }
    }

    private function generateShipmentNumber(): string
    {
        $year = now()->format('Y');
        $lastShipment = Shipment::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastShipment ? ((int) mb_substr((string) $lastShipment->shipment_number, -4)) + 1 : 1;

        return 'SHP-'.$year.'-'.mb_str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}

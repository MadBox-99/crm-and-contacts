<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateShipmentStatusRequest;
use App\Models\Shipment;
use Exception;
use Illuminate\Http\JsonResponse;

final class UpdateShipmentStatusController extends Controller
{
    public function __invoke(UpdateShipmentStatusRequest $request, string $trackingNumber): JsonResponse
    {
        try {
            $shipment = Shipment::where('tracking_number', $trackingNumber)->firstOrFail();

            $shipment->update([
                'status' => $request->status,
                'shipped_at' => $request->shipped_at ?? $shipment->shipped_at,
                'estimated_delivery_at' => $request->estimated_delivery_at ?? $shipment->estimated_delivery_at,
                'delivered_at' => $request->delivered_at ?? $shipment->delivered_at,
                'notes' => $request->notes ?? $shipment->notes,
            ]);

            return response()->json([
                'message' => 'Shipment status updated successfully',
                'data' => $shipment->fresh(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Shipment not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update shipment status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTrackingEventRequest;
use App\Models\Shipment;
use Exception;
use Illuminate\Http\JsonResponse;

final class StoreShipmentTrackingEventController extends Controller
{
    public function __invoke(StoreTrackingEventRequest $request, string $trackingNumber): JsonResponse
    {
        try {
            $shipment = Shipment::where('tracking_number', $trackingNumber)->firstOrFail();

            $trackingEvent = $shipment->trackingEvents()->create([
                'status_code' => $request->status_code,
                'location' => $request->location,
                'description' => $request->description,
                'occurred_at' => $request->occurred_at,
                'metadata' => $request->metadata,
            ]);

            return response()->json([
                'message' => 'Tracking event created successfully',
                'data' => $trackingEvent,
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Shipment not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create tracking event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

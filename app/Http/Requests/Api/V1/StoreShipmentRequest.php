<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // API token authentication
    }

    public function rules(): array
    {
        return [
            // External IDs (one of order_number or external_order_id required)
            'order_number' => ['nullable', 'string', 'exists:orders,order_number'],
            'external_customer_id' => ['nullable', 'string', 'max:255'],
            'external_order_id' => ['nullable', 'string', 'max:255'],

            // Shipment details
            'carrier' => ['required', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255', 'unique:shipments,tracking_number'],
            'status' => ['nullable', 'string', Rule::enum(ShipmentStatus::class)],

            // Shipping address
            'shipping_address' => ['required', 'array'],
            'shipping_address.name' => ['required', 'string', 'max:255'],
            'shipping_address.country' => ['required', 'string', 'max:255'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.city' => ['required', 'string', 'max:255'],
            'shipping_address.street' => ['required', 'string', 'max:255'],
            'shipping_address.building_number' => ['nullable', 'string', 'max:50'],
            'shipping_address.floor' => ['nullable', 'string', 'max:50'],
            'shipping_address.door' => ['nullable', 'string', 'max:50'],

            // Timestamps
            'shipped_at' => ['nullable', 'date'],
            'estimated_delivery_at' => ['nullable', 'date', 'after_or_equal:shipped_at'],
            'delivered_at' => ['nullable', 'date', 'after_or_equal:shipped_at'],

            // Items
            'items' => ['nullable', 'array'],
            'items.*.external_product_id' => ['nullable', 'string', 'max:255'],
            'items.*.product_name' => ['nullable', 'string', 'max:255'],
            'items.*.product_sku' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.weight' => ['nullable', 'numeric', 'min:0'],
            'items.*.length' => ['nullable', 'numeric', 'min:0'],
            'items.*.width' => ['nullable', 'numeric', 'min:0'],
            'items.*.height' => ['nullable', 'numeric', 'min:0'],

            // Notes
            'notes' => ['nullable', 'string'],
        ];
    }
}

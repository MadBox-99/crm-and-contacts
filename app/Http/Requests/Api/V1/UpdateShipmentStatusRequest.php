<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateShipmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // API token authentication
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(ShipmentStatus::class)],
            'shipped_at' => ['nullable', 'date'],
            'estimated_delivery_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

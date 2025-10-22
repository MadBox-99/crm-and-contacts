<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTrackingEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // API token authentication
    }

    public function rules(): array
    {
        return [
            'status_code' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'occurred_at' => ['required', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}

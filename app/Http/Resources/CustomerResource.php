<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unique_identifier' => $this->unique_identifier,
            'name' => $this->name,
            'type' => $this->type,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->when($this->deleted_at, fn () => $this->deleted_at?->toIso8601String()),
        ];
    }
}

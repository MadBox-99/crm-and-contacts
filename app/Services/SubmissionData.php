<?php

declare(strict_types=1);

namespace App\Services;

final readonly class SubmissionData
{
    /**
     * @param  array<string, string>  $utm
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public ?string $email,
        public ?string $name,
        public ?string $phone,
        public ?string $companyName,
        public string $notes,
        public array $utm,
        public ?string $referrer,
        public array $raw,
    ) {}

    public function hasEmail(): bool
    {
        return $this->email !== null && $this->email !== '';
    }
}

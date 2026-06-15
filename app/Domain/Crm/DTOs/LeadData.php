<?php

namespace App\Domain\Crm\DTOs;

readonly class LeadData
{
    public function __construct(
        public string $title,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $description = null,
        public ?string $source = null,
        public array $customFields = [],
        public array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? $data['name'] ?? '',
            firstName: $data['first_name'] ?? $data['firstName'] ?? null,
            lastName: $data['last_name'] ?? $data['lastName'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? $data['mobile'] ?? null,
            company: $data['company'] ?? null,
            description: $data['description'] ?? null,
            source: $data['source'] ?? null,
            customFields: $data['custom_fields'] ?? $data['customFields'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'description' => $this->description,
            'source' => $this->source,
            'custom_fields' => $this->customFields ?: null,
            'metadata' => $this->metadata ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}

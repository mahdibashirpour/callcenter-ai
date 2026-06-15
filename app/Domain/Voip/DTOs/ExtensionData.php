<?php

namespace App\Domain\Voip\DTOs;

readonly class ExtensionData
{
    public function __construct(
        public string $number,
        public ?string $name = null,
        public ?string $password = null,
        public ?string $email = null,
        public bool $enabled = true,
        public array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            number: $data['number'] ?? $data['extension'] ?? '',
            name: $data['name'] ?? null,
            password: $data['password'] ?? null,
            email: $data['email'] ?? null,
            enabled: (bool) ($data['enabled'] ?? true),
            metadata: $data['metadata'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'number' => $this->number,
            'name' => $this->name,
            'password' => $this->password,
            'email' => $this->email,
            'enabled' => $this->enabled,
            'metadata' => $this->metadata ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}

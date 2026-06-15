<?php

namespace App\Domain\Crm\DTOs;

readonly class SyncData
{
    public function __construct(
        public string $entity,
        public ?string $since = null,
        public array $filters = [],
        public array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            entity: $data['entity'] ?? 'all',
            since: $data['since'] ?? null,
            filters: $data['filters'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'entity' => $this->entity,
            'since' => $this->since,
            'filters' => $this->filters ?: null,
            'metadata' => $this->metadata ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}

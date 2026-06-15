<?php

namespace App\Domain\Crm\DTOs;

readonly class TaskData
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?string $dueAt = null,
        public ?string $relatedExternalId = null,
        public ?string $assignee = null,
        public array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            description: $data['description'] ?? null,
            dueAt: $data['due_at'] ?? $data['dueAt'] ?? null,
            relatedExternalId: $data['related_external_id'] ?? $data['relatedExternalId'] ?? null,
            assignee: $data['assignee'] ?? null,
            metadata: $data['metadata'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'due_at' => $this->dueAt,
            'related_external_id' => $this->relatedExternalId,
            'assignee' => $this->assignee,
            'metadata' => $this->metadata ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}

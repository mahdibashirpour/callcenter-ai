<?php

namespace App\Domain\Call\DTOs;

readonly class ManualUploadMetadata
{
    public function __construct(
        public ?string $title = null,
        public ?string $customerName = null,
        public ?string $customerPhone = null,
        public ?string $notes = null,
        public ?string $category = null,
        public ?array $tags = null,
        public ?\DateTimeInterface $conversationDate = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'notes' => $this->notes,
            'category' => $this->category,
            'tags' => $this->tags,
            'conversation_date' => $this->conversationDate?->format('Y-m-d H:i:s'),
        ], fn ($value) => $value !== null && $value !== [] && $value !== '');
    }
}

<?php

namespace App\Domain\Voip\DTOs;

readonly class MakeCallData
{
    public function __construct(
        public string $from,
        public string $to,
        public ?string $callerId = null,
        public ?string $extension = null,
        public array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            from: $data['from'] ?? '',
            to: $data['to'] ?? '',
            callerId: $data['caller_id'] ?? $data['callerId'] ?? null,
            extension: $data['extension'] ?? null,
            metadata: $data['metadata'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'from' => $this->from,
            'to' => $this->to,
            'caller_id' => $this->callerId,
            'extension' => $this->extension,
            'metadata' => $this->metadata ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}

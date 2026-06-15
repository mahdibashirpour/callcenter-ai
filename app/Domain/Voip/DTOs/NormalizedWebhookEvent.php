<?php

namespace App\Domain\Voip\DTOs;

use App\Domain\Voip\Enums\CallDirection;
use App\Domain\Voip\Enums\CallStatus;
use App\Domain\Voip\Enums\VoipEventSource;
use App\Domain\Voip\Enums\VoipWebhookEventType;

readonly class NormalizedWebhookEvent
{
    public function __construct(
        public VoipWebhookEventType $type,
        public ?string $callId = null,
        public ?CallDirection $direction = null,
        public ?string $sourceNumber = null,
        public ?string $destinationNumber = null,
        public ?CallStatus $status = null,
        public ?string $recordingUrl = null,
        public ?string $extension = null,
        public ?string $startedAt = null,
        public ?string $endedAt = null,
        public ?int $duration = null,
        public array $rawPayload = [],
        public VoipEventSource $source = VoipEventSource::Webhook,
        public ?string $provider = null,
    ) {}

    public function withSource(VoipEventSource $source, ?string $provider = null): self
    {
        return new self(
            type: $this->type,
            callId: $this->callId,
            direction: $this->direction,
            sourceNumber: $this->sourceNumber,
            destinationNumber: $this->destinationNumber,
            status: $this->status,
            recordingUrl: $this->recordingUrl,
            extension: $this->extension,
            startedAt: $this->startedAt,
            endedAt: $this->endedAt,
            duration: $this->duration,
            rawPayload: $this->rawPayload,
            source: $source,
            provider: $provider ?? $this->provider,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'call_id' => $this->callId,
            'direction' => $this->direction?->value,
            'source_number' => $this->sourceNumber,
            'destination_number' => $this->destinationNumber,
            'status' => $this->status?->value,
            'recording_url' => $this->recordingUrl,
            'extension' => $this->extension,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'duration' => $this->duration,
            'raw_payload' => $this->rawPayload,
            'source' => $this->source->value,
            'provider' => $this->provider,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: VoipWebhookEventType::tryFrom($data['type'] ?? '') ?? VoipWebhookEventType::Unknown,
            callId: $data['call_id'] ?? $data['callId'] ?? null,
            direction: isset($data['direction']) ? CallDirection::tryFrom($data['direction']) : null,
            sourceNumber: $data['source_number'] ?? $data['sourceNumber'] ?? $data['from'] ?? null,
            destinationNumber: $data['destination_number'] ?? $data['destinationNumber'] ?? $data['to'] ?? null,
            status: isset($data['status']) ? CallStatus::tryFrom($data['status']) : null,
            recordingUrl: $data['recording_url'] ?? $data['recordingUrl'] ?? null,
            extension: $data['extension'] ?? null,
            startedAt: $data['started_at'] ?? $data['startedAt'] ?? null,
            endedAt: $data['ended_at'] ?? $data['endedAt'] ?? null,
            duration: isset($data['duration']) ? (int) $data['duration'] : null,
            rawPayload: $data['raw_payload'] ?? $data['rawPayload'] ?? $data,
            source: VoipEventSource::tryFrom($data['source'] ?? '') ?? VoipEventSource::Webhook,
            provider: $data['provider'] ?? null,
        );
    }
}

<?php

namespace App\Domain\Voip\DTOs;

use App\Domain\Voip\Enums\CallDirection;
use App\Domain\Voip\Enums\CallStatus;

readonly class CallLogData
{
    public function __construct(
        public int $organizationId,
        public int $connectionId,
        public string $providerCode,
        public string $externalCallId,
        public CallDirection $direction,
        public string $sourceNumber,
        public string $destinationNumber,
        public ?CallStatus $status = null,
        public ?string $startedAt = null,
        public ?string $endedAt = null,
        public ?int $duration = null,
        public ?string $recordingUrl = null,
        public ?array $rawPayload = null,
    ) {}

    public function toArray(): array
    {
        return [
            'organization_id' => $this->organizationId,
            'organization_voip_connection_id' => $this->connectionId,
            'provider_code' => $this->providerCode,
            'external_call_id' => $this->externalCallId,
            'direction' => $this->direction->value,
            'source_number' => $this->sourceNumber,
            'destination_number' => $this->destinationNumber,
            'status' => $this->status?->value,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'duration' => $this->duration,
            'recording_url' => $this->recordingUrl,
            'raw_payload' => $this->rawPayload,
        ];
    }
}

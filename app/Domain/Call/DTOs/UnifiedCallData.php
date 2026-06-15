<?php

namespace App\Domain\Call\DTOs;

use App\Domain\Call\Enums\ConversationSource;
use App\Domain\Call\Enums\UploaderType;

readonly class UnifiedCallData
{
    public function __construct(
        public int $organizationId,
        public string $providerCode,
        public string $externalCallId,
        public string $direction,
        public string $callerNumber,
        public string $receiverNumber,
        public ?string $status = null,
        public ?\DateTimeInterface $startedAt = null,
        public ?\DateTimeInterface $endedAt = null,
        public ?int $durationSeconds = null,
        public ?int $organizationVoipConnectionId = null,
        public ?int $organizationUserId = null,
        public ?int $voipCallLogId = null,
        public ?string $recordingUrl = null,
        public ?array $metadata = null,
        public ?int $id = null,
        public ConversationSource $source = ConversationSource::Voip,
        public ?int $uploaderId = null,
        public ?UploaderType $uploaderType = null,
        public ?string $title = null,
        public ?string $customerName = null,
        public ?string $customerPhone = null,
        public ?string $notes = null,
        public ?string $category = null,
        public ?array $tags = null,
        public ?\DateTimeInterface $conversationDate = null,
    ) {}

    public static function fromVoipCallLog(\App\Models\VoipCallLog $log, ?int $organizationUserId = null): self
    {
        return new self(
            organizationId: $log->organization_id,
            providerCode: $log->provider_code,
            externalCallId: $log->external_call_id,
            direction: $log->direction?->value ?? 'outbound',
            callerNumber: $log->source_number,
            receiverNumber: $log->destination_number,
            status: $log->status?->value,
            startedAt: $log->started_at,
            endedAt: $log->ended_at,
            durationSeconds: $log->duration,
            organizationVoipConnectionId: $log->organization_voip_connection_id,
            organizationUserId: $organizationUserId,
            voipCallLogId: $log->id,
            recordingUrl: $log->recording_url,
            metadata: $log->raw_payload,
            source: ConversationSource::Voip,
        );
    }

    public static function forManualUpload(
        int $organizationId,
        ?int $organizationUserId,
        int $uploaderId,
        UploaderType $uploaderType,
        ManualUploadMetadata $metadata,
        ?int $durationSeconds = null,
    ): self {
        return new self(
            organizationId: $organizationId,
            providerCode: 'manual',
            externalCallId: (string) \Illuminate\Support\Str::uuid(),
            direction: 'inbound',
            callerNumber: $metadata->customerPhone ?? 'unknown',
            receiverNumber: 'manual-upload',
            status: 'completed',
            startedAt: $metadata->conversationDate ?? now(),
            endedAt: $metadata->conversationDate ?? now(),
            durationSeconds: $durationSeconds,
            organizationUserId: $organizationUserId,
            source: ConversationSource::ManualUpload,
            uploaderId: $uploaderId,
            uploaderType: $uploaderType,
            title: $metadata->title,
            customerName: $metadata->customerName,
            customerPhone: $metadata->customerPhone,
            notes: $metadata->notes,
            category: $metadata->category,
            tags: $metadata->tags,
            conversationDate: $metadata->conversationDate,
            metadata: $metadata->toArray(),
        );
    }
}

<?php

namespace App\Infrastructure\Call\Repositories;

use App\Domain\Call\Contracts\CallRepositoryInterface;
use App\Domain\Call\DTOs\UnifiedCallData;
use App\Models\Call;

class EloquentCallRepository implements CallRepositoryInterface
{
    public function upsert(UnifiedCallData $data): int
    {
        $call = Call::query()->updateOrCreate(
            [
                'organization_id' => $data->organizationId,
                'provider_code' => $data->providerCode,
                'external_call_id' => $data->externalCallId,
            ],
            [
                'organization_user_id' => $data->organizationUserId,
                'organization_voip_connection_id' => $data->organizationVoipConnectionId,
                'voip_call_log_id' => $data->voipCallLogId,
                'source' => $data->source,
                'uploader_id' => $data->uploaderId,
                'uploader_type' => $data->uploaderType,
                'direction' => $data->direction,
                'caller_number' => $data->callerNumber,
                'receiver_number' => $data->receiverNumber,
                'status' => $data->status ?? 'completed',
                'started_at' => $data->startedAt,
                'ended_at' => $data->endedAt,
                'duration_seconds' => $data->durationSeconds,
                'metadata' => $data->metadata,
                'title' => $data->title,
                'customer_name' => $data->customerName,
                'customer_phone' => $data->customerPhone,
                'notes' => $data->notes,
                'category' => $data->category,
                'tags' => $data->tags,
                'conversation_date' => $data->conversationDate,
            ],
        );

        return $call->id;
    }

    public function findById(int $callId): ?UnifiedCallData
    {
        $call = Call::query()->find($callId);

        return $call ? $this->toDto($call) : null;
    }

    public function findByExternalId(int $organizationId, string $providerCode, string $externalCallId): ?UnifiedCallData
    {
        $call = Call::query()
            ->where('organization_id', $organizationId)
            ->where('provider_code', $providerCode)
            ->where('external_call_id', $externalCallId)
            ->first();

        return $call ? $this->toDto($call) : null;
    }

    private function toDto(Call $call): UnifiedCallData
    {
        return new UnifiedCallData(
            organizationId: $call->organization_id,
            providerCode: $call->provider_code,
            externalCallId: $call->external_call_id,
            direction: $call->direction,
            callerNumber: $call->caller_number,
            receiverNumber: $call->receiver_number,
            status: $call->status,
            startedAt: $call->started_at,
            endedAt: $call->ended_at,
            durationSeconds: $call->duration_seconds,
            organizationVoipConnectionId: $call->organization_voip_connection_id,
            organizationUserId: $call->organization_user_id,
            voipCallLogId: $call->voip_call_log_id,
            metadata: $call->metadata,
            id: $call->id,
            source: $call->source,
            uploaderId: $call->uploader_id,
            uploaderType: $call->uploader_type,
            title: $call->title,
            customerName: $call->customer_name,
            customerPhone: $call->customer_phone,
            notes: $call->notes,
            category: $call->category,
            tags: $call->tags,
            conversationDate: $call->conversation_date,
        );
    }
}

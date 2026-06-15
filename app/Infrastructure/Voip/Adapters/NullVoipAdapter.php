<?php

namespace App\Infrastructure\Voip\Adapters;

use App\Domain\Voip\DTOs\ExtensionData;
use App\Domain\Voip\DTOs\MakeCallData;
use App\Domain\Voip\DTOs\NormalizedWebhookEvent;
use App\Domain\Voip\DTOs\VoipConnectionConfig;
use App\Domain\Voip\Enums\VoipProviderCode;
use App\Domain\Voip\Enums\VoipWebhookEventType;
use App\Domain\Voip\ValueObjects\VoipOperationResult;
use Illuminate\Support\Facades\Log;

class NullVoipAdapter extends AbstractVoipAdapter
{
    private const MESSAGE = 'VoIP adapter is not configured. Operation was skipped.';

    public function getProviderCode(): VoipProviderCode
    {
        return VoipProviderCode::Novatel;
    }

    public function configure(VoipConnectionConfig $config): void
    {
        parent::configure($config);

        Log::warning('Null VoIP adapter configured for connection', [
            'connection_id' => $config->connectionId,
            'organization_id' => $config->organizationId,
        ]);
    }

    public function testConnection(): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function makeCall(MakeCallData $call): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function hangupCall(string $callId): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function getCallDetails(string $callId): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function getCallRecording(string $callId): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function getActiveCalls(): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function createExtension(ExtensionData $extension): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function updateExtension(string $extensionId, ExtensionData $extension): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function getExtensions(): VoipOperationResult
    {
        return $this->skipped(__FUNCTION__);
    }

    public function normalizeWebhook(array $payload): NormalizedWebhookEvent
    {
        Log::warning('Null VoIP adapter received webhook payload', [
            'payload_keys' => array_keys($payload),
        ]);

        return new NormalizedWebhookEvent(
            type: VoipWebhookEventType::Unknown,
            rawPayload: $payload,
        );
    }

    private function skipped(string $operation): VoipOperationResult
    {
        Log::warning('Null VoIP adapter skipped operation', [
            'operation' => $operation,
        ]);

        return VoipOperationResult::failure(self::MESSAGE);
    }
}

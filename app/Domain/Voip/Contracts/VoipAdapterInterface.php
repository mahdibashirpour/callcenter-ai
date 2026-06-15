<?php

namespace App\Domain\Voip\Contracts;

use App\Domain\Voip\DTOs\ExtensionData;
use App\Domain\Voip\DTOs\MakeCallData;
use App\Domain\Voip\DTOs\NormalizedWebhookEvent;
use App\Domain\Voip\DTOs\VoipConnectionConfig;
use App\Domain\Voip\Enums\VoipProviderCode;
use App\Domain\Voip\ValueObjects\VoipOperationResult;

interface VoipAdapterInterface
{
    public function getProviderCode(): VoipProviderCode;

    public function configure(VoipConnectionConfig $config): void;

    public function testConnection(): VoipOperationResult;

    public function makeCall(MakeCallData $call): VoipOperationResult;

    public function hangupCall(string $callId): VoipOperationResult;

    public function getCallDetails(string $callId): VoipOperationResult;

    public function getCallRecording(string $callId): VoipOperationResult;

    public function getActiveCalls(): VoipOperationResult;

    public function createExtension(ExtensionData $extension): VoipOperationResult;

    public function updateExtension(string $extensionId, ExtensionData $extension): VoipOperationResult;

    public function getExtensions(): VoipOperationResult;

    public function normalizeWebhook(array $payload): NormalizedWebhookEvent;
}

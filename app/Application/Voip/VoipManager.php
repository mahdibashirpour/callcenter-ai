<?php

namespace App\Application\Voip;

use App\Application\Voip\Services\VoipConnectionResolver;
use App\Application\Voip\Services\VoipEventIngestionService;
use App\Domain\Voip\Contracts\VoipAdapterInterface;
use App\Domain\Voip\DTOs\ExtensionData;
use App\Domain\Voip\DTOs\MakeCallData;
use App\Domain\Voip\DTOs\VoipConnectionConfig;
use App\Domain\Voip\Enums\VoipEventSource;
use App\Domain\Voip\Enums\VoipOperation;
use App\Domain\Voip\Events\VoipConnectionTested;
use App\Domain\Voip\Exceptions\VoipConnectionNotFoundException;
use App\Domain\Voip\ValueObjects\VoipOperationResult;

class VoipManager
{
    private ?int $organizationId = null;

    private ?int $connectionId = null;

    public function __construct(
        private VoipConnectionResolver $resolver,
        private VoipEventIngestionService $ingestion,
    ) {}

    public static function forOrganization(int $organizationId): self
    {
        $instance = new self(
            resolver: app(VoipConnectionResolver::class),
            ingestion: app(VoipEventIngestionService::class),
        );
        $instance->organizationId = $organizationId;

        return $instance;
    }

    public function connection(?int $connectionId = null): self
    {
        $this->connectionId = $connectionId;

        return $this;
    }

    public function default(): self
    {
        $this->connectionId = null;

        return $this;
    }

    public function testConnection(): VoipOperationResult
    {
        return $this->execute(VoipOperation::TestConnection, fn (VoipAdapterInterface $adapter) => $adapter->testConnection());
    }

    public function makeCall(MakeCallData|array $data): VoipOperationResult
    {
        $call = $data instanceof MakeCallData ? $data : MakeCallData::fromArray($data);

        return $this->execute(VoipOperation::MakeCall, fn (VoipAdapterInterface $adapter) => $adapter->makeCall($call), $call->toArray());
    }

    public function hangupCall(string $callId): VoipOperationResult
    {
        return $this->execute(VoipOperation::HangupCall, fn (VoipAdapterInterface $adapter) => $adapter->hangupCall($callId), ['call_id' => $callId]);
    }

    public function getCallDetails(string $callId): VoipOperationResult
    {
        return $this->execute(VoipOperation::GetCallDetails, fn (VoipAdapterInterface $adapter) => $adapter->getCallDetails($callId), ['call_id' => $callId]);
    }

    public function getCallRecording(string $callId): VoipOperationResult
    {
        return $this->execute(VoipOperation::GetCallRecording, fn (VoipAdapterInterface $adapter) => $adapter->getCallRecording($callId), ['call_id' => $callId]);
    }

    public function getActiveCalls(): VoipOperationResult
    {
        return $this->execute(VoipOperation::GetActiveCalls, fn (VoipAdapterInterface $adapter) => $adapter->getActiveCalls());
    }

    public function createExtension(ExtensionData|array $data): VoipOperationResult
    {
        $extension = $data instanceof ExtensionData ? $data : ExtensionData::fromArray($data);

        return $this->execute(VoipOperation::CreateExtension, fn (VoipAdapterInterface $adapter) => $adapter->createExtension($extension), $extension->toArray());
    }

    public function updateExtension(string $extensionId, ExtensionData|array $data): VoipOperationResult
    {
        $extension = $data instanceof ExtensionData ? $data : ExtensionData::fromArray($data);

        return $this->execute(VoipOperation::UpdateExtension, fn (VoipAdapterInterface $adapter) => $adapter->updateExtension($extensionId, $extension), $extension->toArray());
    }

    public function getExtensions(): VoipOperationResult
    {
        return $this->execute(VoipOperation::GetExtensions, fn (VoipAdapterInterface $adapter) => $adapter->getExtensions());
    }

    public function handleWebhook(array $payload): VoipOperationResult
    {
        [$config, $adapter] = $this->resolver->resolveByConnectionId(
            $this->connectionId ?? throw new VoipConnectionNotFoundException('Connection context is required for webhooks.'),
        );

        $normalized = $adapter
            ->normalizeWebhook($payload)
            ->withSource(VoipEventSource::Webhook, $config->providerCode->value);

        return $this->ingestion->ingest($config, $normalized, $payload);
    }

    private function execute(
        VoipOperation $operation,
        callable $callback,
        ?array $request = null,
    ): VoipOperationResult {
        if ($this->organizationId === null) {
            throw new VoipConnectionNotFoundException('Organization context is required.');
        }

        [$config, $adapter] = $this->resolver->resolve(
            organizationId: $this->organizationId,
            connectionId: $this->connectionId,
        );

        if (! $config->isActive && $operation !== VoipOperation::TestConnection) {
            return VoipOperationResult::failure('VoIP connection is inactive.');
        }

        $result = $callback($adapter);

        if ($operation === VoipOperation::TestConnection) {
            event(new VoipConnectionTested($config->connectionId, $result->status(), $result));
        }

        app(\App\Domain\Voip\Contracts\VoipLogRepositoryInterface::class)->logOperation(
            connectionId: $config->connectionId,
            operation: $operation,
            status: $result->status(),
            request: $request,
            response: $result->data,
            message: $result->message ?? $result->error,
        );

        return $result;
    }
}

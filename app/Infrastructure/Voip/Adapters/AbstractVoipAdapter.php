<?php

namespace App\Infrastructure\Voip\Adapters;

use App\Domain\Voip\Contracts\VoipAdapterInterface;
use App\Domain\Voip\DTOs\VoipConnectionConfig;
use App\Domain\Voip\ValueObjects\VoipOperationResult;

abstract class AbstractVoipAdapter implements VoipAdapterInterface
{
    protected VoipConnectionConfig $config;

    public function configure(VoipConnectionConfig $config): void
    {
        $this->config = $config;
    }

    protected function parseResponse(array $body, string $successMessage, ?string $idKey = 'id'): VoipOperationResult
    {
        if (isset($body['error']) || isset($body['Error']) || ($body['success'] ?? true) === false) {
            return VoipOperationResult::failure(
                error: (string) ($body['error'] ?? $body['Error'] ?? $body['message'] ?? 'Unknown VoIP error'),
                data: $body,
            );
        }

        $data = $body['data'] ?? $body;
        $externalId = $data[$idKey] ?? $data['call_id'] ?? $data['callId'] ?? $data['Id'] ?? null;

        return VoipOperationResult::success(
            externalId: $externalId ? (string) $externalId : null,
            data: is_array($data) ? $data : ['response' => $data],
            message: $successMessage,
        );
    }

    protected function parseHttpFailure(string $message, ?array $data = null): VoipOperationResult
    {
        return VoipOperationResult::failure(error: $message, data: $data);
    }
}

<?php

namespace App\Application\Voip\Actions;

use App\Application\Voip\VoipManager;
use App\Domain\Voip\ValueObjects\VoipOperationResult;

class HandleVoipWebhookAction
{
    public function execute(int $connectionId, array $payload): VoipOperationResult
    {
        return app(VoipManager::class)
            ->connection($connectionId)
            ->handleWebhook($payload);
    }
}

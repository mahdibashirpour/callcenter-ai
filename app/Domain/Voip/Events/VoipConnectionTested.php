<?php

namespace App\Domain\Voip\Events;

use App\Domain\Voip\Enums\VoipLogStatus;
use App\Domain\Voip\ValueObjects\VoipOperationResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoipConnectionTested
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $connectionId,
        public VoipLogStatus $status,
        public VoipOperationResult $result,
    ) {}
}

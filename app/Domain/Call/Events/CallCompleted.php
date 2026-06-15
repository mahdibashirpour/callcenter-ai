<?php

namespace App\Domain\Call\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public int $callId,
        public ?string $recordingUrl = null,
    ) {}
}

<?php

namespace App\Domain\Voip\Events;

use App\Domain\Voip\DTOs\NormalizedWebhookEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallEnded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public int $connectionId,
        public NormalizedWebhookEvent $event,
    ) {}
}

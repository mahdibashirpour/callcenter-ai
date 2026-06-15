<?php

namespace App\Domain\Voip\Contracts;

use App\Domain\Voip\DTOs\NormalizedWebhookEvent;
use DateTimeInterface;

interface VoipPollableAdapterInterface
{
    /**
     * Fetch call events from the provider API for polling-based ingestion.
     *
     * @return list<NormalizedWebhookEvent>
     */
    public function pollCallEvents(?DateTimeInterface $since = null): array;
}

<?php

namespace App\Domain\Llm\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnalysisFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public int $callId,
        public string $reason,
    ) {}
}

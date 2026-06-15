<?php

namespace App\Domain\Crm\DTOs;

readonly class CallIntelligenceSyncData
{
    public function __construct(
        public int $organizationId,
        public int $connectionId,
        public int $analysisId,
        public int $callId,
        public ?int $organizationUserId,
        public string $summary,
        public int $score,
        public string $sentiment,
        public array $strengths,
        public array $weaknesses,
        public array $nextActions,
        public ?string $customerPhone = null,
        public ?array $customerInsights = null,
        public ?array $operationalInsights = null,
        public ?array $leadQuality = null,
        public ?array $concerns = null,
        public ?array $customerIdentity = null,
    ) {}
}

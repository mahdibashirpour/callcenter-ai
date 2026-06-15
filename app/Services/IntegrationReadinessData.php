<?php

namespace App\Services;

use App\Enums\IntegrationSetupStatus;

readonly class IntegrationReadinessData
{
    public function __construct(
        public IntegrationSetupStatus $crmStatus,
        public IntegrationSetupStatus $voipStatus,
    ) {}

    public function systemReady(): bool
    {
        return $this->crmStatus->isComplete() && $this->voipStatus->isComplete();
    }

    /** @return array{crm_status: string, voip_status: string, system_ready: bool} */
    public function toArray(): array
    {
        return [
            'crm_status' => $this->crmStatus->value,
            'voip_status' => $this->voipStatus->value,
            'system_ready' => $this->systemReady(),
        ];
    }
}

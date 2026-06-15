<?php

namespace App\Application\Call\Services;

use App\Models\EmployeeIntegrationMeta;
use App\Models\OrganizationUser;
use App\Models\OrganizationVoipConnection;
use App\Models\VoipCallLog;

class CallEmployeeResolver
{
    public function resolveFromCallLog(VoipCallLog $log): ?int
    {
        $extension = $log->direction?->value === 'inbound'
            ? $log->destination_number
            : $log->source_number;

        if (! $extension) {
            return null;
        }

        $meta = EmployeeIntegrationMeta::query()
            ->where('integratable_type', OrganizationVoipConnection::class)
            ->where('integratable_id', $log->organization_voip_connection_id)
            ->where('key', 'extension')
            ->where('value', $extension)
            ->first();

        return $meta?->organization_user_id;
    }

    public function resolveByExtension(int $organizationId, int $voipConnectionId, string $extension): ?int
    {
        return EmployeeIntegrationMeta::query()
            ->where('integratable_type', OrganizationVoipConnection::class)
            ->where('integratable_id', $voipConnectionId)
            ->where('key', 'extension')
            ->where('value', $extension)
            ->whereHas('employee', fn ($q) => $q->where('organization_id', $organizationId))
            ->value('organization_user_id');
    }
}

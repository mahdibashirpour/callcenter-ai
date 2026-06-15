<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrganizationActivity;
use App\Models\User;

class EmployerContext
{
    public static function organization(): Organization
    {
        $organization = auth()->user()?->primaryOrganization();

        if (! $organization) {
            abort(403, 'No organization found for this employer.');
        }

        return $organization;
    }

    public static function organizationId(): int
    {
        return self::organization()->id;
    }

    public static function integrationReadiness(): IntegrationReadinessData
    {
        return app(OrganizationIntegrationStatusService::class)
            ->forOrganization(self::organization());
    }
}

class OrganizationActivityLogger
{
    public static function log(
        int $organizationId,
        string $type,
        string $title,
        ?string $description = null,
        ?array $metadata = null,
        ?User $user = null,
    ): void {
        OrganizationActivity::query()->create([
            'organization_id' => $organizationId,
            'user_id' => $user?->id ?? auth()->id(),
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}

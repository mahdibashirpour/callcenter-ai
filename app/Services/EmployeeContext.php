<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrganizationUser;

class EmployeeContext
{
    public static function membership(): OrganizationUser
    {
        $membership = OrganizationUser::query()
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (! $membership) {
            abort(403, 'No active employee membership found.');
        }

        return $membership;
    }

    public static function organization(): Organization
    {
        return self::membership()->organization;
    }

    public static function organizationId(): int
    {
        return self::organization()->id;
    }
}

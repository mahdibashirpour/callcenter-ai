<?php

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\OrganizationUser;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('organization.{organizationId}.incoming-calls', function ($user, int $organizationId) {
    if ($user->role === UserRole::Employer) {
        return Organization::query()
            ->whereKey($organizationId)
            ->where('user_id', $user->id)
            ->exists();
    }

    if ($user->role === UserRole::Employee) {
        return OrganizationUser::query()
            ->where('organization_id', $organizationId)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }

    return $user->role === UserRole::SuperAdmin || $user->role === UserRole::Admin;
});

Broadcast::channel('organization.{organizationId}.processing-queue', function ($user, int $organizationId) {
    if ($user->role === UserRole::Employer) {
        return Organization::query()
            ->whereKey($organizationId)
            ->where('user_id', $user->id)
            ->exists();
    }

    if ($user->role === UserRole::Employee) {
        return OrganizationUser::query()
            ->where('organization_id', $organizationId)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }

    return $user->role === UserRole::SuperAdmin || $user->role === UserRole::Admin;
});

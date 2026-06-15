<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User;
use App\Models\UserImpersonationLog;
use Illuminate\Http\Request;

class ImpersonationService
{
    public const SESSION_ORIGINAL_ADMIN_ID = 'impersonation.original_admin_id';

    public const SESSION_LOG_ID = 'impersonation.log_id';

    public const SESSION_STARTED_AT = 'impersonation.started_at';

    public function canImpersonate(User $admin, User $target): bool
    {
        if (! $admin->role->canAccessSuperAdminFeatures()) {
            return false;
        }

        if ($admin->id === $target->id) {
            return false;
        }

        if (in_array($target->role, [UserRole::SuperAdmin, UserRole::Admin], true)) {
            return false;
        }

        if (! in_array($target->role, [UserRole::Employer, UserRole::Employee], true)) {
            return false;
        }

        return match ($target->role) {
            UserRole::Employer => $this->employerCanBeImpersonated($target),
            UserRole::Employee => $this->employeeCanBeImpersonated($target),
            default => false,
        };
    }

    public function impersonationDeniedReason(User $admin, User $target): ?string
    {
        if (! $admin->role->canAccessSuperAdminFeatures()) {
            return __('filament.impersonation.only_super_admin');
        }

        if ($admin->id === $target->id) {
            return __('filament.impersonation.cannot_self');
        }

        if (in_array($target->role, [UserRole::SuperAdmin, UserRole::Admin], true)) {
            return __('filament.impersonation.cannot_admin');
        }

        return match ($target->role) {
            UserRole::Employer => $this->employerDeniedReason($target),
            UserRole::Employee => $this->employeeDeniedReason($target),
            default => __('filament.impersonation.not_allowed'),
        };
    }

    public function isImpersonating(): bool
    {
        return session()->has(self::SESSION_ORIGINAL_ADMIN_ID)
            && session()->has(self::SESSION_LOG_ID);
    }

    public function originalAdminId(): ?int
    {
        return session(self::SESSION_ORIGINAL_ADMIN_ID);
    }

    public function originalAdmin(): ?User
    {
        $id = $this->originalAdminId();

        return $id ? User::query()->find($id) : null;
    }

    public function activeLog(): ?UserImpersonationLog
    {
        $logId = session(self::SESSION_LOG_ID);

        return $logId ? UserImpersonationLog::query()->find($logId) : null;
    }

    /** @return array{target: User, original_admin: User, started_at: string}|null */
    public function context(): ?array
    {
        if (! $this->isImpersonating()) {
            return null;
        }

        $originalAdmin = $this->originalAdmin();
        $target = auth()->user();

        if (! $originalAdmin || ! $target) {
            return null;
        }

        return [
            'target' => $target,
            'original_admin' => $originalAdmin,
            'started_at' => session(self::SESSION_STARTED_AT),
        ];
    }

    public function resolveOrganization(User $target): ?Organization
    {
        return match ($target->role) {
            UserRole::Employer => $target->primaryOrganization(),
            UserRole::Employee => $target->employeeOrganizations()
                ->wherePivot('is_active', true)
                ->first(),
            default => null,
        };
    }

    public function start(User $admin, User $target, Request $request): UserImpersonationLog
    {
        if (! $this->canImpersonate($admin, $target)) {
            throw new \RuntimeException($this->impersonationDeniedReason($admin, $target) ?? __('filament.impersonation.not_allowed'));
        }

        $organization = $this->resolveOrganization($target);

        $log = UserImpersonationLog::query()->create([
            'admin_user_id' => $admin->id,
            'target_user_id' => $target->id,
            'organization_id' => $organization?->id,
            'started_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        session([
            self::SESSION_ORIGINAL_ADMIN_ID => $admin->id,
            self::SESSION_LOG_ID => $log->id,
            self::SESSION_STARTED_AT => now()->toIso8601String(),
        ]);

        return $log;
    }

    public function stop(): User
    {
        if (! $this->isImpersonating()) {
            throw new \RuntimeException(__('filament.impersonation.no_active_session'));
        }

        $originalAdminId = $this->originalAdminId();
        $log = $this->activeLog();

        $log?->update(['ended_at' => now()]);

        session()->forget([
            self::SESSION_ORIGINAL_ADMIN_ID,
            self::SESSION_LOG_ID,
            self::SESSION_STARTED_AT,
        ]);

        $admin = User::query()->findOrFail($originalAdminId);

        return $admin;
    }

    private function employerCanBeImpersonated(User $target): bool
    {
        return $this->employerDeniedReason($target) === null;
    }

    private function employeeCanBeImpersonated(User $target): bool
    {
        return $this->employeeDeniedReason($target) === null;
    }

    private function employerDeniedReason(User $target): ?string
    {
        $organization = $target->primaryOrganization();

        if (! $organization) {
            return __('filament.impersonation.employer_no_org');
        }

        if ($organization->disabled) {
            return __('filament.impersonation.employer_suspended');
        }

        return null;
    }

    private function employeeDeniedReason(User $target): ?string
    {
        $membership = OrganizationUser::query()
            ->where('user_id', $target->id)
            ->where('is_active', true)
            ->first();

        if (! $membership) {
            return __('filament.impersonation.employee_inactive');
        }

        if ($membership->organization?->disabled) {
            return __('filament.impersonation.employee_org_suspended');
        }

        return null;
    }
}

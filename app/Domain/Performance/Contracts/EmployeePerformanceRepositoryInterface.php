<?php

namespace App\Domain\Performance\Contracts;

interface EmployeePerformanceRepositoryInterface
{
    public function recalculateForEmployee(int $organizationId, int $organizationUserId): void;

    public function recalculateForOrganization(int $organizationId): void;
}

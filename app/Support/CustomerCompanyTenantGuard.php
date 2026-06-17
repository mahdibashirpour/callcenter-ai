<?php

namespace App\Support;

use App\Models\CustomerCompany;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerCompanyTenantGuard
{
    public static function assertCompanyInOrganization(CustomerCompany $company, int $organizationId): void
    {
        if ($company->organization_id !== $organizationId) {
            throw new NotFoundHttpException;
        }
    }
}

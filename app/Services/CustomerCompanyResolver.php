<?php

namespace App\Services;

use App\Models\CustomerCompany;

class CustomerCompanyResolver
{
    public function findOrCreate(int $organizationId, string $name): CustomerCompany
    {
        $trimmed = trim($name);
        $normalized = CustomerCompany::normalizeName($trimmed);

        if ($normalized === '') {
            throw new \InvalidArgumentException('Company name cannot be empty.');
        }

        $company = CustomerCompany::query()->firstOrCreate(
            [
                'organization_id' => $organizationId,
                'normalized_name' => $normalized,
            ],
            [
                'name' => $trimmed,
            ],
        );

        if ($company->name !== $trimmed && mb_strlen($trimmed) > mb_strlen($company->name)) {
            $company->update(['name' => $trimmed]);
        }

        return $company->fresh();
    }
}

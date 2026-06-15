<?php

namespace App\Domain\Crm\Exceptions;

use Exception;

class CrmConnectionNotFoundException extends Exception
{
    public static function forOrganization(int $organizationId, ?int $connectionId = null): self
    {
        if ($connectionId) {
            return new self("CRM connection [{$connectionId}] not found for organization [{$organizationId}].");
        }

        return new self("No CRM connection found for organization [{$organizationId}].");
    }
}

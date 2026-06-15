<?php

namespace App\Domain\Voip\Exceptions;

use Exception;

class VoipConnectionNotFoundException extends Exception
{
    public static function forOrganization(int $organizationId, ?int $connectionId = null): self
    {
        if ($connectionId) {
            return new self("VoIP connection [{$connectionId}] not found for organization [{$organizationId}].");
        }

        return new self("No VoIP connection found for organization [{$organizationId}].");
    }
}

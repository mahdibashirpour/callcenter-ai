<?php

namespace App\Domain\Crm\Exceptions;

use App\Domain\Crm\Enums\CrmProviderCode;
use Exception;

class CrmAdapterNotFoundException extends Exception
{
    public static function forProvider(CrmProviderCode|string $provider): self
    {
        $code = $provider instanceof CrmProviderCode ? $provider->value : $provider;

        return new self("CRM adapter not registered for provider [{$code}].");
    }
}

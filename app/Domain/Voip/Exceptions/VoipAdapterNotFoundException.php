<?php

namespace App\Domain\Voip\Exceptions;

use App\Domain\Voip\Enums\VoipProviderCode;
use Exception;

class VoipAdapterNotFoundException extends Exception
{
    public static function forProvider(VoipProviderCode|string $provider): self
    {
        $code = $provider instanceof VoipProviderCode ? $provider->value : $provider;

        return new self("VoIP adapter not registered for provider [{$code}].");
    }
}

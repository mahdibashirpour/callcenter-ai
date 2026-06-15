<?php

namespace App\Domain\Voip\Exceptions;

use App\Domain\Voip\Contracts\VoipAdapterInterface;
use Exception;

class VoipAdapterInstantiationException extends Exception
{
    public static function notFound(string $class): self
    {
        return new self("VoIP adapter class not found: {$class}");
    }

    public static function invalidInterface(string $class): self
    {
        return new self(
            "VoIP adapter class [{$class}] must implement ".VoipAdapterInterface::class
        );
    }

    public static function instantiationFailed(string $class, string $reason): self
    {
        return new self("Failed to instantiate VoIP adapter [{$class}]: {$reason}");
    }
}

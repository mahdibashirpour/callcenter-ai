<?php

namespace App\Exceptions;

use RuntimeException;

class DemoCleanupException extends RuntimeException
{
    public static function notDemoOrganization(): self
    {
        return new self('Only demo organizations can be removed with this action.');
    }
}

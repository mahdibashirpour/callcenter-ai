<?php

namespace App\Domain\Crm\Exceptions;

use App\Domain\Crm\Enums\CrmOperation;
use Exception;

class CrmOperationException extends Exception
{
    public static function failed(CrmOperation $operation, string $message): self
    {
        return new self("CRM operation [{$operation->value}] failed: {$message}");
    }
}

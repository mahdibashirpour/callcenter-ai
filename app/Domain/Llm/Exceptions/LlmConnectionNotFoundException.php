<?php

namespace App\Domain\Llm\Exceptions;

use Exception;

class LlmConnectionNotFoundException extends Exception
{
    public static function forOrganization(int $organizationId): self
    {
        return new self("No active LLM connection found for organization [{$organizationId}].");
    }
}

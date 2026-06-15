<?php

namespace App\Domain\Llm\Exceptions;

use App\Domain\Llm\Enums\LlmProviderCode;
use Exception;

class LlmProviderNotFoundException extends Exception
{
    public static function forProvider(LlmProviderCode|string $provider): self
    {
        $code = $provider instanceof LlmProviderCode ? $provider->value : $provider;

        return new self("LLM provider [{$code}] is not registered.");
    }
}

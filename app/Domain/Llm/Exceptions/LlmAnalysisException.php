<?php

namespace App\Domain\Llm\Exceptions;

use Exception;

class LlmAnalysisException extends Exception
{
    public static function invalidResponse(string $reason): self
    {
        return new self("LLM analysis failed: {$reason}");
    }
}

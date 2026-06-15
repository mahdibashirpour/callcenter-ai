<?php

namespace App\Domain\Llm\ValueObjects;

readonly class TokenUsage
{
    public function __construct(
        public int $inputTokens,
        public int $outputTokens,
    ) {}

    public function total(): int
    {
        return $this->inputTokens + $this->outputTokens;
    }
}

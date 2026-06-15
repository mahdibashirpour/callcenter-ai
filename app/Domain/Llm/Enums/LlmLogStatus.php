<?php

namespace App\Domain\Llm\Enums;

enum LlmLogStatus: string
{
    case Success = 'success';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'موفق',
            self::Failed => 'ناموفق',
        };
    }
}

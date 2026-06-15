<?php

namespace App\Domain\Llm\Enums;

enum LlmOperation: string
{
    case TestConnection = 'test_connection';
    case AnalyzeAudio = 'analyze_audio';

    public function label(): string
    {
        return match ($this) {
            self::TestConnection => 'آزمایش اتصال',
            self::AnalyzeAudio => 'تحلیل صوتی',
        };
    }
}

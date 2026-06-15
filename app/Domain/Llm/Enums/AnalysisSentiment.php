<?php

namespace App\Domain\Llm\Enums;

enum AnalysisSentiment: string
{
    case Positive = 'positive';
    case Neutral = 'neutral';
    case Negative = 'negative';
    case Mixed = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::Positive => 'مثبت',
            self::Neutral => 'خنثی',
            self::Negative => 'منفی',
            self::Mixed => 'ترکیبی',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Positive => 'success',
            self::Neutral => 'gray',
            self::Negative => 'danger',
            self::Mixed => 'warning',
        };
    }
}

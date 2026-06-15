<?php

namespace App\Domain\AiUsage\Enums;

enum UsageAggregationPeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'روزانه',
            self::Weekly => 'هفتگی',
            self::Monthly => 'ماهانه',
        };
    }
}

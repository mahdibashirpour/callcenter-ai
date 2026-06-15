<?php

namespace App\Domain\Performance\Enums;

enum PerformancePeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Overall = 'overall';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'روزانه',
            self::Weekly => 'هفتگی',
            self::Monthly => 'ماهانه',
            self::Overall => 'کلی',
        };
    }
}

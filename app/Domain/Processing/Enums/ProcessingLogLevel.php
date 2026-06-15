<?php

namespace App\Domain\Processing\Enums;

enum ProcessingLogLevel: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Debug = 'debug';

    public function label(): string
    {
        return match ($this) {
            self::Info => 'اطلاعات',
            self::Warning => 'هشدار',
            self::Error => 'خطا',
            self::Debug => 'اشکال‌زدایی',
        };
    }
}

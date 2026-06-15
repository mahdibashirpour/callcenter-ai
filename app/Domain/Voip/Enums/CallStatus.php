<?php

namespace App\Domain\Voip\Enums;

enum CallStatus: string
{
    case Initiated = 'initiated';
    case Ringing = 'ringing';
    case Answered = 'answered';
    case Completed = 'completed';
    case Missed = 'missed';
    case Failed = 'failed';
    case Busy = 'busy';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Initiated => 'آغاز شده',
            self::Ringing => 'در حال زنگ',
            self::Answered => 'پاسخ داده شده',
            self::Completed => 'تکمیل شده',
            self::Missed => 'از دست رفته',
            self::Failed => 'ناموفق',
            self::Busy => 'مشغول',
            self::Cancelled => 'لغو شده',
        };
    }
}

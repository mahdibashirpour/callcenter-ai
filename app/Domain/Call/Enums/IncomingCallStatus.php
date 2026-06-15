<?php

namespace App\Domain\Call\Enums;

enum IncomingCallStatus: string
{
    case Ringing = 'ringing';
    case Claimed = 'claimed';
    case Missed = 'missed';
    case Completed = 'completed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Ringing => 'در حال زنگ',
            self::Claimed => 'دریافت شده',
            self::Missed => 'از دست رفته',
            self::Completed => 'تکمیل شده',
            self::Expired => 'منقضی شده',
        };
    }
}

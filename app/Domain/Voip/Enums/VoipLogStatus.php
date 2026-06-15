<?php

namespace App\Domain\Voip\Enums;

enum VoipLogStatus: string
{
    case Success = 'success';
    case Failed = 'failed';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'موفق',
            self::Failed => 'ناموفق',
            self::Pending => 'در انتظار',
        };
    }
}

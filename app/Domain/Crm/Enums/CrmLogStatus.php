<?php

namespace App\Domain\Crm\Enums;

enum CrmLogStatus: string
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

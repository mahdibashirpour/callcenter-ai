<?php

namespace App\Domain\Voip\Enums;

enum VoipEventSource: string
{
    case Webhook = 'webhook';
    case Polling = 'polling';

    public function label(): string
    {
        return match ($this) {
            self::Webhook => 'وب‌هوک',
            self::Polling => 'نظرسنجی',
        };
    }
}

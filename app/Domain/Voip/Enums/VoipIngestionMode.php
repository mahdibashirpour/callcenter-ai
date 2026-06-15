<?php

namespace App\Domain\Voip\Enums;

enum VoipIngestionMode: string
{
    case Webhook = 'webhook';
    case Polling = 'polling';
    case Hybrid = 'hybrid';

    public function label(): string
    {
        return match ($this) {
            self::Webhook => 'وب‌هوک',
            self::Polling => 'نظرسنجی',
            self::Hybrid => 'ترکیبی',
        };
    }

    public function usesWebhook(): bool
    {
        return in_array($this, [self::Webhook, self::Hybrid], true);
    }

    public function usesPolling(): bool
    {
        return in_array($this, [self::Polling, self::Hybrid], true);
    }
}

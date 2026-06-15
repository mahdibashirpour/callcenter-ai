<?php

namespace App\Domain\Call\Enums;

enum ConversationSource: string
{
    case Voip = 'voip';
    case ManualUpload = 'manual_upload';
    case Api = 'api';
    case Imported = 'imported';

    public function label(): string
    {
        return match ($this) {
            self::Voip => 'تماس VoIP',
            self::ManualUpload => 'آپلود دستی',
            self::Api => 'API',
            self::Imported => 'واردشده',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $source) => [$source->value => $source->label()])
            ->all();
    }
}

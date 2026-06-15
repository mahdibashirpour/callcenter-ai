<?php

namespace App\Domain\Billing\Enums;

enum ConversationEstimateType: string
{
    case ShortSupport = 'short_support';
    case Sales = 'sales';
    case Consultation = 'consultation';
    case LongMeeting = 'long_meeting';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::ShortSupport => 'تماس کوتاه پشتیبانی',
            self::Sales => 'تماس فروش',
            self::Consultation => 'مشاوره',
            self::LongMeeting => 'جلسه طولانی',
            self::Custom => 'سفارشی',
        };
    }

    public function defaultOutputRatio(): float
    {
        return match ($this) {
            self::ShortSupport => 0.15,
            self::Sales => 0.25,
            self::Consultation => 0.35,
            self::LongMeeting => 0.50,
            self::Custom => 0.30,
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->all();
    }
}

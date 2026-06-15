<?php

namespace App\Domain\Billing\Enums;

enum WalletTransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case AiUsage = 'ai_usage';
    case Refund = 'refund';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'واریز',
            self::Withdrawal => 'برداشت',
            self::AiUsage => 'مصرف هوش مصنوعی',
            self::Refund => 'بازپرداخت',
            self::Adjustment => 'تعدیل',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->all();
    }
}

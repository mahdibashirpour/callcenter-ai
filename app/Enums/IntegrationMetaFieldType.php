<?php

namespace App\Enums;

enum IntegrationMetaFieldType: string
{
    case Text = 'text';
    case Email = 'email';
    case Tel = 'tel';
    case Number = 'number';
    case Password = 'password';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'متن',
            self::Email => 'ایمیل',
            self::Tel => 'تلفن',
            self::Number => 'عدد',
            self::Password => 'رمز عبور',
        };
    }
}

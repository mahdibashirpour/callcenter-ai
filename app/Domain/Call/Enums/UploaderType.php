<?php

namespace App\Domain\Call\Enums;

enum UploaderType: string
{
    case Employer = 'employer';
    case Employee = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::Employer => 'کارفرما',
            self::Employee => 'کارشناس',
        };
    }
}

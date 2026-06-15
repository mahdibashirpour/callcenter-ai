<?php

namespace App\Domain\Voip\Enums;

enum VoipProviderCode: string
{
    case Novatel = 'novatel';

    public function label(): string
    {
        return 'Navatel';
    }
}

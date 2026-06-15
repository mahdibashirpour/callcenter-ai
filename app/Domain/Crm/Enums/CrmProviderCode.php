<?php

namespace App\Domain\Crm\Enums;

enum CrmProviderCode: string
{
    case Didar = 'didar';

    public function label(): string
    {
        return 'Didar CRM';
    }
}

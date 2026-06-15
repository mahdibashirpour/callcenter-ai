<?php

namespace App\Enums;

enum IntegrationSetupStatus: string
{
    case Complete = 'complete';
    case Incomplete = 'incomplete';

    public function isComplete(): bool
    {
        return $this === self::Complete;
    }
}

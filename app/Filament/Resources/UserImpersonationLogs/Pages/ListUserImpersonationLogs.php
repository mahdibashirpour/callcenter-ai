<?php

namespace App\Filament\Resources\UserImpersonationLogs\Pages;

use App\Filament\Resources\UserImpersonationLogs\UserImpersonationLogResource;
use Filament\Resources\Pages\ListRecords;

class ListUserImpersonationLogs extends ListRecords
{
    protected static string $resource = UserImpersonationLogResource::class;
}

<?php

namespace App\Filament\Resources\OrganizationVoipConnections\Pages;

use App\Filament\Resources\OrganizationVoipConnections\OrganizationVoipConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationVoipConnections extends ListRecords
{
    protected static string $resource = OrganizationVoipConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

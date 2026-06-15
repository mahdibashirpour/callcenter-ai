<?php

namespace App\Filament\Resources\OrganizationCrmConnections\Pages;

use App\Filament\Resources\OrganizationCrmConnections\OrganizationCrmConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationCrmConnections extends ListRecords
{
    protected static string $resource = OrganizationCrmConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

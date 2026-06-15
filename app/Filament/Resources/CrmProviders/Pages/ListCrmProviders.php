<?php

namespace App\Filament\Resources\CrmProviders\Pages;

use App\Filament\Resources\CrmProviders\CrmProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrmProviders extends ListRecords
{
    protected static string $resource = CrmProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

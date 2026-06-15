<?php

namespace App\Filament\Resources\CrmProviders\Pages;

use App\Filament\Resources\CrmProviders\CrmProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrmProvider extends EditRecord
{
    protected static string $resource = CrmProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

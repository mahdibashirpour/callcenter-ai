<?php

namespace App\Filament\Resources\VoipProviders\Pages;

use App\Filament\Resources\VoipProviders\VoipProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVoipProviders extends ListRecords
{
    protected static string $resource = VoipProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

<?php

namespace App\Filament\Resources\VoipProviders\Pages;

use App\Filament\Resources\VoipProviders\VoipProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVoipProvider extends EditRecord
{
    protected static string $resource = VoipProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

<?php

namespace App\Filament\Resources\VoipProviders\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VoipProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText(__('filament.misc.provider_code_novatel')),
                TextInput::make('adapter_class')
                    ->label(__('filament.fields.adapter_class'))
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label(__('filament.fields.active'))
                    ->default(true),
                KeyValue::make('config')
                    ->label(__('filament.descriptions.provider_configuration'))
                    ->keyLabel(__('filament.fields.key'))
                    ->valueLabel(__('filament.fields.value'))
                    ->reorderable(),
            ]);
    }
}

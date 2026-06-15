<?php

namespace App\Filament\Resources\OrganizationVoipConnections\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationVoipConnectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.sections.connection'))
                    ->schema([
                        Select::make('organization_id')
                            ->relationship('organization', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        Select::make('voip_provider_id')
                            ->relationship('provider', 'name', fn ($query) => $query->where('is_active', true))
                            ->label(__('filament.fields.voip_provider'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_default')
                            ->label(__('filament.fields.default_connection')),
                        Toggle::make('is_active')
                            ->label(__('filament.fields.active'))
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make(__('filament.sections.credentials'))
                    ->schema([
                        TextInput::make('credentials.api_url')
                            ->label(__('filament.fields.api_url'))
                            ->url()
                            ->required()
                            ->default('https://api.navatel.ir/v1'),
                        TextInput::make('credentials.api_key')
                            ->label(__('filament.fields.api_key'))
                            ->password()
                            ->revealable(),
                        TextInput::make('credentials.api_token')
                            ->label(__('filament.fields.api_token'))
                            ->password()
                            ->revealable(),
                        TextInput::make('credentials.username')
                            ->label(__('filament.fields.username')),
                        TextInput::make('credentials.password')
                            ->label(__('filament.fields.password'))
                            ->password()
                            ->revealable(),
                    ])
                    ->columns(2),
                Section::make(__('filament.sections.settings'))
                    ->schema([
                        TextInput::make('settings.webhook_url')
                            ->label(__('filament.fields.webhook_url'))
                            ->url()
                            ->helperText(__('filament.misc.voip_webhook_helper')),
                        TextInput::make('settings.webhook_secret')
                            ->label(__('filament.fields.webhook_secret'))
                            ->password()
                            ->revealable(),
                        KeyValue::make('settings.extension_mapping')
                            ->label(__('filament.fields.extension_mapping'))
                            ->keyLabel(__('filament.fields.extension'))
                            ->valueLabel(__('filament.fields.mapped_value')),
                        KeyValue::make('settings.recording_settings')
                            ->label(__('filament.fields.recording_settings'))
                            ->keyLabel(__('filament.fields.key'))
                            ->valueLabel(__('filament.fields.value')),
                        TextInput::make('settings.timeout')
                            ->label(__('filament.fields.timeout_seconds'))
                            ->persianNumeric(0)
                            ->default(30)
                            ->minValue(5)
                            ->maxValue(120),
                    ])
                    ->columns(2),
            ]);
    }
}

<?php

namespace App\Filament\Resources\OrganizationCrmConnections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationCrmConnectionForm
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
                        Select::make('crm_provider_id')
                            ->relationship('provider', 'name', fn ($query) => $query->where('is_active', true))
                            ->label(__('filament.fields.crm_provider'))
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
                            ->default('https://app.didar.me/api'),
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
                    ->description(__('filament.misc.crm_settings_description'))
                    ->schema([
                        TextInput::make('settings.webhook_url')
                            ->label(__('filament.fields.webhook_url'))
                            ->url(),
                        TextInput::make('settings.webhook_secret')
                            ->label(__('filament.fields.webhook_secret'))
                            ->password()
                            ->revealable(),
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

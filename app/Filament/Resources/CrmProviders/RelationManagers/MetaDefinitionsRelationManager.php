<?php

namespace App\Filament\Resources\CrmProviders\RelationManagers;

use App\Enums\IntegrationMetaFieldType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MetaDefinitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'metaDefinitions';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_managers.meta_field_definitions');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText(__('filament.misc.internal_key_crm_helper')),
                Select::make('field_type')
                    ->options(IntegrationMetaFieldType::class)
                    ->required()
                    ->native(false),
                Toggle::make('is_required')
                    ->label(__('filament.fields.required')),
                TextInput::make('placeholder')
                    ->maxLength(255),
                Textarea::make('help_text')
                    ->rows(2),
                TextInput::make('sort_order')
                    ->persianNumeric(0)
                    ->default(0)
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->badge()
                    ->searchable(),
                TextColumn::make('field_type')
                    ->badge(),
                IconColumn::make('is_required')
                    ->label(__('filament.fields.required'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Resources\OrganizationVoipConnections\OrganizationVoipConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VoipConnectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'voipConnections';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_managers.voip_connections');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->url(fn ($record) => OrganizationVoipConnectionResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('provider.name')
                    ->label(__('filament.fields.provider'))
                    ->badge(),
                IconColumn::make('is_default')
                    ->label(__('filament.fields.default'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(__('filament.fields.active'))
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn () => OrganizationVoipConnectionResource::getUrl('create', [
                        'organization_id' => $this->getOwnerRecord()->getKey(),
                    ])),
            ])
            ->defaultSort('name');
    }
}

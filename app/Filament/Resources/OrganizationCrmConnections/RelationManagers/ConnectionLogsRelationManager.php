<?php

namespace App\Filament\Resources\OrganizationCrmConnections\RelationManagers;

use App\Domain\Crm\Enums\CrmLogStatus;
use App\Domain\Crm\Enums\CrmOperation;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConnectionLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'connectionLogs';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_managers.connection_logs');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('operation')
                    ->badge()
                    ->formatStateUsing(fn (CrmOperation $state): string => $state->label()),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CrmLogStatus $state): string => $state->label())
                    ->color(fn (CrmLogStatus $state): string => match ($state) {
                        CrmLogStatus::Success => 'success',
                        CrmLogStatus::Failed => 'danger',
                        CrmLogStatus::Pending => 'warning',
                    }),
                TextColumn::make('message')
                    ->limit(80)
                    ->placeholder(__('filament.misc.em_dash')),
                TextColumn::make('created_at')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}

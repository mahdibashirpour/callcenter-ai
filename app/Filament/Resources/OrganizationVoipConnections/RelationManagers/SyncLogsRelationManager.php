<?php

namespace App\Filament\Resources\OrganizationVoipConnections\RelationManagers;

use App\Domain\Voip\Enums\VoipLogStatus;
use App\Domain\Voip\Enums\VoipOperation;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SyncLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'syncLogs';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_managers.sync_logs');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('operation')
                    ->badge()
                    ->formatStateUsing(fn (VoipOperation $state): string => $state->label()),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (VoipLogStatus $state): string => $state->label())
                    ->color(fn (VoipLogStatus $state): string => match ($state) {
                        VoipLogStatus::Success => 'success',
                        VoipLogStatus::Failed => 'danger',
                        VoipLogStatus::Pending => 'warning',
                    }),
                TextColumn::make('records_processed')
                    ->label(__('filament.fields.records'))
                    ->placeholder(__('filament.misc.em_dash')),
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

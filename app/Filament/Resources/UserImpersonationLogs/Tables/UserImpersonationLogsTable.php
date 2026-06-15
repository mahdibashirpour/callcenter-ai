<?php

namespace App\Filament\Resources\UserImpersonationLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class UserImpersonationLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admin.name')
                    ->label(__('filament.fields.admin'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('targetUser.name')
                    ->label(__('filament.fields.target_user'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('targetUser.role')
                    ->label(__('filament.fields.role'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? __('filament.misc.em_dash')),
                TextColumn::make('organization.title')
                    ->label(__('filament.fields.organization'))
                    ->placeholder(__('filament.misc.em_dash'))
                    ->searchable(),
                TextColumn::make('started_at')
                    ->label(__('filament.fields.start_time'))
                    ->jalaliDateTime()
                    ->sortable(),
                TextColumn::make('ended_at')
                    ->label(__('filament.fields.end_time'))
                    ->jalaliDateTime()
                    ->placeholder(__('filament.fields.active'))
                    ->sortable(),
                TextColumn::make('duration')
                    ->label(__('filament.fields.duration'))
                    ->getStateUsing(fn ($record) => $record->durationLabel() ?? ($record->isActive() ? __('filament.misc.in_progress') : __('filament.misc.em_dash'))),
                TextColumn::make('ip_address')
                    ->label(__('filament.fields.ip'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('admin_user_id')
                    ->label(__('filament.fields.admin'))
                    ->relationship('admin', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('target_user_id')
                    ->label(__('filament.fields.target_user'))
                    ->relationship('targetUser', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('started_between')
                    ->schema([
                        DatePicker::make('from')->jalali()->label(__('filament.fields.from')),
                        DatePicker::make('until')->jalali()->label(__('filament.fields.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('started_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('started_at', '<=', $date));
                    }),
            ])
            ->defaultSort('started_at', 'desc')
            ->paginated([25, 50, 100]);
    }
}

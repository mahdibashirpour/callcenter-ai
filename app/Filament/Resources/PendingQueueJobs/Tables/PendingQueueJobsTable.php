<?php

namespace App\Filament\Resources\PendingQueueJobs\Tables;

use App\Models\PendingQueueJob;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PendingQueueJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('job_class')
                    ->label(__('filament.fields.job_class'))
                    ->getStateUsing(fn (PendingQueueJob $record) => $record->jobClassLabel())
                    ->searchable(query: function ($query, string $search) {
                        $query->where('payload', 'like', '%'.$search.'%');
                    }),
                TextColumn::make('call_id')
                    ->label(__('filament.fields.call_id'))
                    ->getStateUsing(fn (PendingQueueJob $record) => $record->callId())
                    ->placeholder(__('filament.misc.em_dash')),
                TextColumn::make('queue')->badge()->sortable(),
                TextColumn::make('attempts')->sortable(),
                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->getStateUsing(fn (PendingQueueJob $record) => $record->isReserved()
                        ? __('filament.status.processing')
                        : __('filament.status.queued'))
                    ->color(fn (PendingQueueJob $record) => $record->isReserved() ? 'info' : 'warning'),
                TextColumn::make('queued_at')
                    ->label(__('filament.fields.queued_at'))
                    ->getStateUsing(fn (PendingQueueJob $record) => $record->queuedAt())
                    ->jalaliDateTime()
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('created_at', $direction)),
            ])
            ->filters([
                SelectFilter::make('queue')
                    ->options(fn () => PendingQueueJob::query()->distinct()->orderBy('queue')->pluck('queue', 'queue')->all()),
                TernaryFilter::make('reserved')
                    ->label(__('filament.fields.status'))
                    ->nullable()
                    ->trueLabel(__('filament.status.processing'))
                    ->falseLabel(__('filament.status.queued'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('reserved_at'),
                        false: fn (Builder $query) => $query->whereNull('reserved_at'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }
}

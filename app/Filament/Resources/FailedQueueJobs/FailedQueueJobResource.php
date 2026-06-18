<?php

namespace App\Filament\Resources\FailedQueueJobs;

use App\Filament\Concerns\OnlySuperAdmin;
use App\Filament\Resources\FailedQueueJobs\Pages\ListFailedQueueJobs;
use App\Filament\Resources\FailedQueueJobs\Pages\ViewFailedQueueJob;
use App\Filament\Resources\FailedQueueJobs\Tables\FailedQueueJobsTable;
use App\Models\FailedQueueJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FailedQueueJobResource extends Resource
{
    use OnlySuperAdmin;

    protected static ?string $model = FailedQueueJob::class;

    protected static ?string $slug = 'failed-queue-jobs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.failed_queue_jobs');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('filament.navigation.groups.system');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.failed_queue_job');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.failed_queue_jobs');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = FailedQueueJob::query()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return FailedQueueJob::query()->exists() ? 'danger' : 'gray';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return FailedQueueJobsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFailedQueueJobs::route('/'),
            'view' => ViewFailedQueueJob::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

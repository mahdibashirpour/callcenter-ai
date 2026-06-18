<?php

namespace App\Filament\Widgets\System;

use App\Enums\UserRole;
use App\Filament\Resources\FailedQueueJobs\FailedQueueJobResource;
use App\Filament\Resources\PendingQueueJobs\PendingQueueJobResource;
use App\Services\QueueMonitoring\QueueMonitorStats;
use App\Support\PersianNumber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QueueOverviewStats extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }

    protected function getStats(): array
    {
        $stats = app(QueueMonitorStats::class)->overview();

        return [
            Stat::make(__('filament.widgets.queue_pending'), PersianNumber::format($stats['pending'], 0))
                ->description(__('filament.misc.queue_pending_description'))
                ->color('warning')
                ->url(PendingQueueJobResource::getUrl('index')),
            Stat::make(__('filament.widgets.queue_reserved'), PersianNumber::format($stats['reserved'], 0))
                ->description(__('filament.misc.queue_reserved_description'))
                ->color('info')
                ->url(PendingQueueJobResource::getUrl('index')),
            Stat::make(__('filament.widgets.queue_failed'), PersianNumber::format($stats['failed'], 0))
                ->description(__('filament.misc.queue_failed_description'))
                ->color($stats['failed'] > 0 ? 'danger' : 'success')
                ->url(FailedQueueJobResource::getUrl('index')),
            Stat::make(__('filament.widgets.queue_batches'), PersianNumber::format($stats['batches'], 0))
                ->description(__('filament.misc.queue_batches_description'))
                ->color('gray'),
        ];
    }
}

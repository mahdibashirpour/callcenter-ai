<?php

namespace App\Filament\Pages\System;

use App\Filament\Concerns\OnlySuperAdmin;
use App\Filament\Widgets\System\QueueOverviewStats;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class QueueMonitor extends Page
{
    use OnlySuperAdmin;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?int $navigationSort = 1;

    protected static string $routePath = 'system/queue-monitor';

    protected string $view = 'filament.pages.system.queue-monitor';

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.queue_monitor');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.system');
    }

    public function getTitle(): string
    {
        return __('filament.pages.queue_monitor');
    }

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | \Filament\Widgets\WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return [QueueOverviewStats::class];
    }
}

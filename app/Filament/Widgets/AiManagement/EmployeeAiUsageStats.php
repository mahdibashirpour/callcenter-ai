<?php

namespace App\Filament\Widgets\AiManagement;

use App\Enums\UserRole;
use App\Models\PlatformAiSettings;
use App\Support\PersianNumber;
use App\Services\AiUsageAnalyticsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeAiUsageStats extends StatsOverviewWidget
{
    public ?int $organizationUserId = null;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }

    protected function getStats(): array
    {
        if (! $this->organizationUserId) {
            return [];
        }

        $overview = app(AiUsageAnalyticsService::class)->employeeOverview($this->organizationUserId);

        return [
            Stat::make(__('filament.widgets.input_tokens'), PersianNumber::format($overview['input_tokens'], 0)),
            Stat::make(__('filament.widgets.output_tokens'), PersianNumber::format($overview['output_tokens'], 0)),
            Stat::make(__('filament.widgets.total_tokens'), PersianNumber::format($overview['total_tokens'], 0)),
            Stat::make(__('filament.fields.total_cost'), PlatformAiSettings::formatMoney($overview['total_cost'])),
            Stat::make(__('filament.widgets.conversations'), PersianNumber::format($overview['analyses_count'], 0)),
            Stat::make(__('filament.widgets.average_score'), PersianNumber::format($overview['average_score'], 0)),
        ];
    }
}

<?php

namespace App\Filament\Widgets\AiManagement;

use App\Enums\UserRole;
use App\Models\PlatformAiSettings;
use App\Support\PersianNumber;
use App\Services\AiUsageAnalyticsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrganizationAiUsageStats extends StatsOverviewWidget
{
    public ?int $organizationId = null;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }

    protected function getStats(): array
    {
        if (! $this->organizationId) {
            return [];
        }

        $overview = app(AiUsageAnalyticsService::class)->organizationOverview($this->organizationId);

        return [
            Stat::make(__('filament.widgets.input_tokens'), PersianNumber::format($overview['input_tokens'], 0)),
            Stat::make(__('filament.widgets.output_tokens'), PersianNumber::format($overview['output_tokens'], 0)),
            Stat::make(__('filament.widgets.total_tokens'), PersianNumber::format($overview['total_tokens'], 0)),
            Stat::make(__('filament.fields.total_cost'), PlatformAiSettings::formatMoney($overview['total_cost'])),
            Stat::make(__('filament.widgets.analyses'), PersianNumber::format($overview['analyses_count'], 0)),
            Stat::make(__('filament.widgets.avg_cost_per_analysis'), PlatformAiSettings::formatMoney($overview['average_cost_per_analysis'])),
        ];
    }
}

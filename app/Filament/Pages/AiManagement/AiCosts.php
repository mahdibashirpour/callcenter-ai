<?php

namespace App\Filament\Pages\AiManagement;

use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Filament\Concerns\OnlySuperAdmin;
use App\Filament\Widgets\AiManagement\AiAnalysisTrendChart;
use App\Filament\Widgets\AiManagement\AiCostTrendChart;
use App\Filament\Widgets\AiManagement\PlatformAiUsageStats;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\WidgetConfiguration;
use UnitEnum;

class AiCosts extends Page
{
    use OnlySuperAdmin;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?int $navigationSort = 2;

    protected static string $routePath = 'ai-management/ai-costs';

    public UsageAggregationPeriod $chartPeriod = UsageAggregationPeriod::Daily;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.ai_costs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.ai_management');
    }

    public function getTitle(): string
    {
        return __('filament.pages.ai_costs');
    }

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return [PlatformAiUsageStats::class];
    }

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | WidgetConfiguration>
     */
    protected function getFooterWidgets(): array
    {
        return [
            AiCostTrendChart::make(['period' => UsageAggregationPeriod::Daily]),
            AiCostTrendChart::make(['period' => UsageAggregationPeriod::Weekly, 'chartHeading' => __('filament.widgets.weekly_cost')]),
            AiCostTrendChart::make(['period' => UsageAggregationPeriod::Monthly, 'chartHeading' => __('filament.widgets.monthly_cost')]),
            AiAnalysisTrendChart::make(['period' => UsageAggregationPeriod::Daily]),
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}

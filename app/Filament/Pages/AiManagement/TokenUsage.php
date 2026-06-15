<?php

namespace App\Filament\Pages\AiManagement;

use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Filament\Concerns\OnlySuperAdmin;
use App\Enums\UserRole;
use App\Filament\Widgets\AiManagement\AiAnalysisTrendChart;
use App\Filament\Widgets\AiManagement\AiCostTrendChart;
use App\Filament\Widgets\AiManagement\AiUsageTrendChart;
use App\Filament\Widgets\AiManagement\PlatformAiUsageStats;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\WidgetConfiguration;
use UnitEnum;

class TokenUsage extends Page
{
    use OnlySuperAdmin;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?int $navigationSort = 1;

    protected static string $routePath = 'ai-management/token-usage';

    public UsageAggregationPeriod $chartPeriod = UsageAggregationPeriod::Daily;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.token_usage');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.ai_management');
    }

    public function getTitle(): string
    {
        return __('filament.pages.token_usage');
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
            AiUsageTrendChart::make(['period' => $this->chartPeriod, 'metric' => 'input_tokens', 'chartHeading' => __('filament.widgets.input_tokens')]),
            AiUsageTrendChart::make(['period' => $this->chartPeriod, 'metric' => 'output_tokens', 'chartHeading' => __('filament.widgets.output_tokens')]),
            AiUsageTrendChart::make(['period' => $this->chartPeriod, 'metric' => 'total_tokens', 'chartHeading' => __('filament.widgets.total_tokens')]),
            AiAnalysisTrendChart::make(['period' => $this->chartPeriod]),
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}

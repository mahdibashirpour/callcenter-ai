<?php

namespace App\Filament\Widgets\AiManagement;

use App\Models\PlatformAiSettings;
use App\Enums\UserRole;
use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Services\AiUsageAnalyticsService;
use Filament\Widgets\ChartWidget;

class AiCostTrendChart extends ChartWidget
{
    public ?int $organizationId = null;

    public ?int $organizationUserId = null;

    public UsageAggregationPeriod $period = UsageAggregationPeriod::Daily;

    public ?string $chartHeading = null;

    public function getHeading(): ?string
    {
        return $this->chartHeading ?? __('filament.widgets.ai_cost_trend');
    }

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $service = app(AiUsageAnalyticsService::class);

        $trend = match (true) {
            $this->organizationUserId !== null => $service->employeeTrend($this->organizationUserId, $this->period),
            $this->organizationId !== null => $service->organizationTrend($this->organizationId, $this->period),
            default => $service->platformTrend($this->period),
        };

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.cost_with_currency', ['currency' => PlatformAiSettings::currencyCode()]),
                    'data' => array_column($trend, 'total_cost'),
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => array_column($trend, 'period'),
        ];
    }
}

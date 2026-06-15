<?php

namespace App\Filament\Widgets\AiManagement;

use App\Enums\UserRole;
use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Services\AiUsageAnalyticsService;
use Filament\Widgets\ChartWidget;

class AiAnalysisTrendChart extends ChartWidget
{
    public ?int $organizationId = null;

    public UsageAggregationPeriod $period = UsageAggregationPeriod::Daily;

    public ?string $chartHeading = null;

    public function getHeading(): ?string
    {
        return $this->chartHeading ?? __('filament.widgets.analysis_activity');
    }

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $service = app(AiUsageAnalyticsService::class);

        $trend = $this->organizationId
            ? $service->organizationTrend($this->organizationId, $this->period)
            : $service->platformTrend($this->period);

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.analyses'),
                    'data' => array_column($trend, 'analyses_count'),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => array_column($trend, 'period'),
        ];
    }
}

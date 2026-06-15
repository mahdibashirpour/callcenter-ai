<?php

namespace App\Filament\Widgets\AiManagement;

use App\Enums\UserRole;
use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Services\AiUsageAnalyticsService;
use Filament\Widgets\ChartWidget;

class AiUsageTrendChart extends ChartWidget
{
    public ?int $organizationId = null;

    public ?int $organizationUserId = null;

    public UsageAggregationPeriod $period = UsageAggregationPeriod::Daily;

    public ?string $chartHeading = null;

    public string $metric = 'total_tokens';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }

    public function getHeading(): ?string
    {
        return $this->chartHeading ?? __('filament.widgets.token_consumption');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getMetricLabel(): string
    {
        return match ($this->metric) {
            'input_tokens' => __('filament.widgets.input_tokens'),
            'output_tokens' => __('filament.widgets.output_tokens'),
            'total_tokens' => __('filament.widgets.total_tokens'),
            default => str($this->metric)->replace('_', ' ')->title()->toString(),
        };
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
                    'label' => $this->getMetricLabel(),
                    'data' => array_map(fn (array $row) => $row[$this->metric] ?? 0, $trend),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => array_column($trend, 'period'),
        ];
    }
}

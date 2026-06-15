<?php

namespace App\Filament\Resources\EmployeeAiConsumption\Pages;

use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Filament\Resources\EmployeeAiConsumption\EmployeeAiConsumptionResource;
use App\Filament\Widgets\AiManagement\AiCostTrendChart;
use App\Filament\Widgets\AiManagement\AiUsageTrendChart;
use App\Filament\Widgets\AiManagement\EmployeeAiUsageStats;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Widgets\WidgetConfiguration;

class ViewEmployeeAiConsumption extends ViewRecord
{
    protected static string $resource = EmployeeAiConsumptionResource::class;

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            EmployeeAiUsageStats::make(['organizationUserId' => $this->getRecord()->id]),
        ];
    }

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | WidgetConfiguration>
     */
    protected function getFooterWidgets(): array
    {
        $employeeId = $this->getRecord()->id;

        return [
            AiUsageTrendChart::make(['organizationUserId' => $employeeId, 'period' => UsageAggregationPeriod::Daily, 'metric' => 'total_tokens', 'chartHeading' => __('filament.misc.daily_usage')]),
            AiUsageTrendChart::make(['organizationUserId' => $employeeId, 'period' => UsageAggregationPeriod::Weekly, 'metric' => 'total_tokens', 'chartHeading' => __('filament.misc.weekly_usage')]),
            AiUsageTrendChart::make(['organizationUserId' => $employeeId, 'period' => UsageAggregationPeriod::Monthly, 'metric' => 'total_tokens', 'chartHeading' => __('filament.misc.monthly_usage')]),
            AiCostTrendChart::make(['organizationUserId' => $employeeId, 'period' => UsageAggregationPeriod::Daily]),
        ];
    }

    public function getTitle(): string
    {
        return __('filament.misc.ai_usage_title', ['name' => $this->getRecord()->full_name]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getRelationManagersContentComponent(),
            ]);
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}

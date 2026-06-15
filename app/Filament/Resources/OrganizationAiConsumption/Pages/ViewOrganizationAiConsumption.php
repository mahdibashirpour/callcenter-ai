<?php

namespace App\Filament\Resources\OrganizationAiConsumption\Pages;

use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Filament\Resources\OrganizationAiConsumption\OrganizationAiConsumptionResource;
use App\Filament\Widgets\AiManagement\AiAnalysisTrendChart;
use App\Filament\Widgets\AiManagement\AiCostTrendChart;
use App\Filament\Widgets\AiManagement\AiUsageTrendChart;
use App\Filament\Widgets\AiManagement\OrganizationAiUsageStats;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Widgets\WidgetConfiguration;

class ViewOrganizationAiConsumption extends ViewRecord
{
    protected static string $resource = OrganizationAiConsumptionResource::class;

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            OrganizationAiUsageStats::make(['organizationId' => $this->getRecord()->id]),
        ];
    }

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | WidgetConfiguration>
     */
    protected function getFooterWidgets(): array
    {
        $orgId = $this->getRecord()->id;

        return [
            AiUsageTrendChart::make(['organizationId' => $orgId, 'period' => UsageAggregationPeriod::Daily, 'metric' => 'total_tokens', 'chartHeading' => __('filament.misc.daily_tokens')]),
            AiUsageTrendChart::make(['organizationId' => $orgId, 'period' => UsageAggregationPeriod::Weekly, 'metric' => 'total_tokens', 'chartHeading' => __('filament.misc.weekly_tokens')]),
            AiUsageTrendChart::make(['organizationId' => $orgId, 'period' => UsageAggregationPeriod::Monthly, 'metric' => 'total_tokens', 'chartHeading' => __('filament.misc.monthly_tokens')]),
            AiCostTrendChart::make(['organizationId' => $orgId, 'period' => UsageAggregationPeriod::Daily]),
            AiAnalysisTrendChart::make(['organizationId' => $orgId, 'period' => UsageAggregationPeriod::Daily]),
        ];
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

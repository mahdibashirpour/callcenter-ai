<?php

namespace App\Filament\Resources\LlmModels\Widgets;

use App\Models\LlmModel;
use App\Models\PlatformAiSettings;
use App\Services\AiCostEstimatorService;
use Filament\Widgets\Widget;

class ModelCostBreakdownWidget extends Widget
{
    protected string $view = 'filament.resources.llm-models.widgets.model-cost-breakdown';

    protected int|string|array $columnSpan = 'full';

    public ?LlmModel $record = null;

    /** @return array<string, mixed> */
    public function getSummary(): array
    {
        if (! $this->record) {
            return [];
        }

        return app(AiCostEstimatorService::class)->modelCostSummary($this->record);
    }

    public function formatMoney(float $amount): string
    {
        return PlatformAiSettings::formatMoney($amount);
    }
}

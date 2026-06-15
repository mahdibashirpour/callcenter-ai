<?php

namespace App\Filament\Resources\LlmModels\Pages;

use App\Filament\Resources\LlmModels\Pages\CostEstimator;
use App\Filament\Resources\LlmModels\LlmModelResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListLlmModels extends ListRecords
{
    protected static string $resource = LlmModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('costEstimator')
                ->label(__('filament.navigation.cost_estimator'))
                ->icon(Heroicon::OutlinedCalculator)
                ->url(CostEstimator::getUrl()),
            CreateAction::make(),
        ];
    }
}

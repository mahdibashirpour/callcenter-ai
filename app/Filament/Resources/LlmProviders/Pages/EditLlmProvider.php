<?php

namespace App\Filament\Resources\LlmProviders\Pages;

use App\Filament\Resources\LlmProviders\LlmProviderResource;
use App\Models\LlmModel;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLlmProvider extends EditRecord
{
    protected static string $resource = LlmProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalDescription(__('filament.misc.delete_provider_warning')),
        ];
    }

    protected function afterSave(): void
    {
        $this->syncDefaultModel();
    }

    private function syncDefaultModel(): void
    {
        $provider = $this->record->refresh();

        if (! $provider->default_llm_model_id) {
            return;
        }

        LlmModel::query()
            ->where('provider_id', $provider->id)
            ->whereKeyNot($provider->default_llm_model_id)
            ->update(['is_default' => false]);

        LlmModel::query()
            ->whereKey($provider->default_llm_model_id)
            ->update(['is_default' => true]);
    }
}

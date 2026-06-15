<?php

namespace App\Filament\Resources\EmployeeAiConsumption\RelationManagers;

use App\Filament\Resources\ConversationAnalyses\ConversationAnalysisResource;
use App\Models\PlatformAiSettings;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeAnalysesRelationManager extends RelationManager
{
    protected static string $relationship = 'conversationAnalyses';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_managers.conversation_usage_history');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('analyzed_at')
                    ->label(__('filament.fields.call_date'))
                    ->jalaliDateTime()
                    ->sortable(),
                TextColumn::make('model_name')->badge(),
                TextColumn::make('input_tokens')->numeric(),
                TextColumn::make('output_tokens')->numeric(),
                TextColumn::make('total_tokens')->numeric(),
                TextColumn::make('cost')->money(fn () => PlatformAiSettings::currencyCode()),
                TextColumn::make('processing_duration_ms')->label(__('filament.fields.processing_ms')),
                TextColumn::make('score')->badge(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => ConversationAnalysisResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('analyzed_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}

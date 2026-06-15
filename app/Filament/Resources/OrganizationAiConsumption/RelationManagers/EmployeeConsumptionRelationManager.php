<?php

namespace App\Filament\Resources\OrganizationAiConsumption\RelationManagers;

use App\Filament\Resources\EmployeeAiConsumption\EmployeeAiConsumptionResource;
use App\Models\PlatformAiSettings;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeConsumptionRelationManager extends RelationManager
{
    protected static string $relationship = 'memberships';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_managers.employee_token_breakdown');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withCount('conversationAnalyses as total_analyses')
                ->withSum('conversationAnalyses as total_input_tokens', 'input_tokens')
                ->withSum('conversationAnalyses as total_output_tokens', 'output_tokens')
                ->withSum('conversationAnalyses as total_tokens_sum', 'total_tokens')
                ->withSum('conversationAnalyses as total_ai_cost', 'cost')
                ->withAvg('conversationAnalyses as average_score', 'score')
                ->withMax('conversationAnalyses as last_analysis_at', 'analyzed_at'))
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('filament.fields.employee'))
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('department')
                    ->placeholder(__('filament.misc.em_dash')),
                TextColumn::make('total_analyses')
                    ->label(__('filament.widgets.analyses'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_input_tokens')
                    ->label(__('filament.fields.input_tokens'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_output_tokens')
                    ->label(__('filament.fields.output_tokens'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_tokens_sum')
                    ->label(__('filament.fields.total_tokens'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_ai_cost')
                    ->label(__('filament.widgets.cost'))
                    ->money(fn () => PlatformAiSettings::currencyCode())
                    ->sortable(),
                TextColumn::make('average_score')
                    ->label(__('filament.fields.avg_score'))
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),
                TextColumn::make('last_analysis_at')
                    ->label(__('filament.fields.last_analysis'))
                    ->jalaliDateTime()
                    ->placeholder(__('filament.misc.em_dash'))
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => EmployeeAiConsumptionResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('total_tokens_sum', 'desc')
            ->paginated([10, 25, 50]);
    }
}

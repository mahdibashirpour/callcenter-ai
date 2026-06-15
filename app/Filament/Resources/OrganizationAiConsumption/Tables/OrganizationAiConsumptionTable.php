<?php

namespace App\Filament\Resources\OrganizationAiConsumption\Tables;

use App\Filament\Resources\OrganizationAiConsumption\OrganizationAiConsumptionResource;
use App\Filament\Support\OrganizationConsumptionExporter;
use App\Models\PlatformAiSettings;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrganizationAiConsumptionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament.fields.organization'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_employees')
                    ->label(__('filament.fields.employees'))
                    ->numeric()
                    ->sortable(),
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
                    ->label(__('filament.fields.ai_cost'))
                    ->money(fn () => PlatformAiSettings::currencyCode())
                    ->sortable(),
                TextColumn::make('last_analysis_at')
                    ->label(__('filament.fields.last_analysis'))
                    ->jalaliDateTime()
                    ->sortable()
                    ->placeholder(__('filament.misc.em_dash')),
            ])
            ->filters([
                Filter::make('analyzed_between')
                    ->schema([
                        DatePicker::make('from')->jalali()->label(__('filament.fields.from')),
                        DatePicker::make('until')->jalali()->label(__('filament.fields.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['from'] && ! $data['until']) {
                            return $query;
                        }

                        return app(\App\Services\AiUsageAnalyticsService::class)
                            ->organizationsWithUsageQuery(
                                $data['from'] ? Carbon::parse($data['from'])->startOfDay() : null,
                                $data['until'] ? Carbon::parse($data['until'])->endOfDay() : null,
                            );
                    }),
            ])
            ->headerActions([
                Action::make('exportCsv')
                    ->label(__('filament.actions.export_csv'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => OrganizationConsumptionExporter::downloadCsv()),
                Action::make('exportExcel')
                    ->label(__('filament.actions.export_excel'))
                    ->icon('heroicon-o-table-cells')
                    ->action(fn () => OrganizationConsumptionExporter::downloadExcel()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('total_tokens_sum', 'desc')
            ->paginated([25, 50, 100]);
    }
}

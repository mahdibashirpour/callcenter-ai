<?php

namespace App\Filament\Resources\OrganizationAiConsumption;

use App\Filament\Concerns\OnlySuperAdmin;
use App\Filament\Resources\OrganizationAiConsumption\Pages\ListOrganizationAiConsumption;
use App\Filament\Resources\OrganizationAiConsumption\Pages\ViewOrganizationAiConsumption;
use App\Filament\Resources\OrganizationAiConsumption\RelationManagers\EmployeeConsumptionRelationManager;
use App\Filament\Resources\OrganizationAiConsumption\RelationManagers\OrganizationAnalysesRelationManager;
use App\Filament\Resources\OrganizationAiConsumption\Tables\OrganizationAiConsumptionTable;
use App\Models\Organization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrganizationAiConsumptionResource extends Resource
{
    use OnlySuperAdmin;

    protected static ?string $model = Organization::class;

    protected static ?string $slug = 'organization-ai-consumption';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.organization_consumption');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('filament.navigation.groups.ai_management');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.organization');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.organizations');
    }

    public static function getEloquentQuery(): Builder
    {
        return app(\App\Services\AiUsageAnalyticsService::class)->organizationsWithUsageQuery();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return OrganizationAiConsumptionTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EmployeeConsumptionRelationManager::class,
            OrganizationAnalysesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationAiConsumption::route('/'),
            'view' => ViewOrganizationAiConsumption::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}

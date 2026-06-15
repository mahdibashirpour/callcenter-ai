<?php

namespace App\Filament\Resources\EmployeeAiConsumption;

use App\Enums\UserRole;
use App\Filament\Resources\EmployeeAiConsumption\Pages\ViewEmployeeAiConsumption;
use App\Filament\Resources\EmployeeAiConsumption\RelationManagers\EmployeeAnalysesRelationManager;
use App\Models\OrganizationUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmployeeAiConsumptionResource extends Resource
{
    protected static ?string $model = OrganizationUser::class;

    protected static ?string $slug = 'employee-ai-consumption';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('filament.navigation.groups.ai_management');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === UserRole::SuperAdmin;
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getRelations(): array
    {
        return [
            EmployeeAnalysesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewEmployeeAiConsumption::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

<?php

namespace App\Filament\Resources\UserImpersonationLogs;

use App\Filament\Concerns\OnlySuperAdmin;
use App\Filament\Resources\UserImpersonationLogs\Pages\ListUserImpersonationLogs;
use App\Filament\Resources\UserImpersonationLogs\Tables\UserImpersonationLogsTable;
use App\Models\UserImpersonationLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserImpersonationLogResource extends Resource
{
    use OnlySuperAdmin;

    protected static ?string $model = UserImpersonationLog::class;

    protected static ?string $slug = 'user-impersonation-logs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.impersonation_logs');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('filament.navigation.groups.security');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.impersonation_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.impersonation_logs');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['admin', 'targetUser', 'organization']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return UserImpersonationLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserImpersonationLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

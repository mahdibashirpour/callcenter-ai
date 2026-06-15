<?php

namespace App\Filament\Resources\ConversationAnalyses;

use App\Filament\Concerns\OnlySuperAdmin;
use App\Filament\Resources\ConversationAnalyses\Pages\ListConversationAnalyses;
use App\Filament\Resources\ConversationAnalyses\Pages\ViewConversationAnalysis;
use App\Filament\Resources\ConversationAnalyses\Tables\ConversationAnalysesTable;
use App\Models\ConversationAnalysis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConversationAnalysisResource extends Resource
{
    use OnlySuperAdmin;

    protected static ?string $model = ConversationAnalysis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.analysis_activity');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('filament.navigation.groups.ai_management');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.analysis');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.analyses');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['organization', 'employee', 'callLog']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return ConversationAnalysesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConversationAnalyses::route('/'),
            'view' => ViewConversationAnalysis::route('/{record}'),
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

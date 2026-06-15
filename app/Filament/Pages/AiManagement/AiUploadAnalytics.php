<?php

namespace App\Filament\Pages\AiManagement;

use App\Filament\Concerns\OnlySuperAdmin;
use App\Services\AiUploadAnalyticsService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AiUploadAnalytics extends Page
{
    use OnlySuperAdmin;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?int $navigationSort = 5;

    protected static string $routePath = 'ai-management/upload-analytics';

    protected string $view = 'filament.pages.ai-management.ai-upload-analytics';

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.ai_upload_analytics');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.ai_management');
    }

    public function getTitle(): string
    {
        return __('filament.pages.ai_upload_analytics');
    }

    public function getViewData(): array
    {
        $analytics = app(AiUploadAnalyticsService::class);

        return [
            'overview' => $analytics->platformOverview(),
            'perOrganization' => $analytics->uploadsPerOrganization(),
            'perEmployee' => $analytics->uploadsPerEmployee(),
        ];
    }
}

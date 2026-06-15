<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Organizations\OrganizationResource;
use App\Filament\Support\DemoOrganizationCleanupActions;
use App\Services\Demo\DemoOrganizationCleanupService;
use App\Support\Seeding\DemoCatalog;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    public function getSubheading(): ?string
    {
        if (auth()->user()?->role !== UserRole::SuperAdmin) {
            return null;
        }

        if (app(DemoOrganizationCleanupService::class)->demoOrganizationCount() === 0) {
            return null;
        }

        return __('filament.demo_credentials.subheading', [
            'password' => DemoCatalog::DEMO_PASSWORD,
            'domain' => DemoCatalog::EMAIL_DOMAIN,
            'example' => DemoCatalog::exampleEmployerLogin(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DemoOrganizationCleanupActions::managementGroup(),
            CreateAction::make(),
        ];
    }
}

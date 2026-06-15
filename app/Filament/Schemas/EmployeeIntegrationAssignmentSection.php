<?php

namespace App\Filament\Schemas;

use App\Services\EmployeeIntegrationMetaService;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class EmployeeIntegrationAssignmentSection
{
    public static function make(int $organizationId): Section
    {
        return Section::make(__('filament.sections.integration_assignments'))
            ->description(__('filament.sections.integration_assignments_description'))
            ->schema([
                Repeater::make('integration_assignments')
                    ->label(__('filament.fields.connections'))
                    ->schema([
                        Select::make('connection')
                            ->label(__('filament.fields.integration_connection'))
                            ->options(fn () => EmployeeIntegrationMetaService::connectionOptionsForOrganization($organizationId))
                            ->required()
                            ->searchable()
                            ->live()
                            ->distinct()
                            ->native(false),
                        Group::make()
                            ->schema(fn (Get $get): array => EmployeeIntegrationMetaService::formFieldsForConnection($get('connection')))
                            ->visible(fn (Get $get): bool => filled($get('connection'))),
                    ])
                    ->columns(1)
                    ->addActionLabel(__('filament.integration.add_connection'))
                    ->defaultItems(0)
                    ->collapsible(),
            ]);
    }
}

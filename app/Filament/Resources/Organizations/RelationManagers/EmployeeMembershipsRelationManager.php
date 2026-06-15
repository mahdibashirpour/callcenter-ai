<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Models\Organization;
use App\Filament\Support\DemoUserActions;
use App\Filament\Schemas\EmployeeIntegrationAssignmentSection;
use App\Services\EmployeeIntegrationMetaService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeMembershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'memberships';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_managers.employee_integrations');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmployeeIntegrationAssignmentSection::make($this->getOwnerRecord()->getKey()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('filament.fields.name'))
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('user.email')
                    ->label(__('filament.fields.email'))
                    ->searchable(),
                TextColumn::make('integration_meta_count')
                    ->counts('integrationMeta')
                    ->label(__('filament.fields.mappings')),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        $data['integration_assignments'] = EmployeeIntegrationMetaService::assignmentsFromEmployee($record);

                        return $data;
                    })
                    ->using(function ($record, array $data) {
                        EmployeeIntegrationMetaService::syncForEmployee(
                            $record,
                            $data['integration_assignments'] ?? [],
                        );

                        return $record;
                    }),
                DeleteAction::make(),
            ])
            ->headerActions([
                DemoUserActions::addEmployee(fn (): Organization => $this->getOwnerRecord()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

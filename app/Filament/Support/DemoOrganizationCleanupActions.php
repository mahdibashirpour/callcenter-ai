<?php

namespace App\Filament\Support;

use App\Filament\Resources\Organizations\OrganizationResource;
use App\Enums\UserRole;
use App\Exceptions\DemoCleanupException;
use App\Models\Organization;
use App\Services\Demo\DemoOrganizationCleanupService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

final class DemoOrganizationCleanupActions
{
    public static function deleteAll(): Action
    {
        return Action::make('deleteAllDemoOrganizations')
            ->label(__('filament.actions.delete_all_demo_organizations'))
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->button()
            ->visible(fn (): bool => auth()->user()?->role === UserRole::SuperAdmin)
            ->requiresConfirmation()
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->modalIconColor('danger')
            ->modalHeading(__('filament.demo_cleanup.delete_all_heading'))
            ->modalDescription(fn (): string => app(DemoOrganizationCleanupService::class)
                ->summarizeAll()
                ->toDeleteAllModalDescription())
            ->modalSubmitActionLabel(__('filament.actions.delete_all_demo_organizations'))
            ->modalCancelActionLabel(__('filament.actions.cancel'))
            ->disabled(fn (): bool => app(DemoOrganizationCleanupService::class)->demoOrganizationCount() === 0)
            ->tooltip(fn (): ?string => app(DemoOrganizationCleanupService::class)->demoOrganizationCount() === 0
                ? __('filament.demo_cleanup.no_demo_data')
                : null)
            ->action(function (): void {
                try {
                    $summary = app(DemoOrganizationCleanupService::class)->deleteAll();
                } catch (DemoCleanupException $exception) {
                    Notification::make()
                        ->title(__('filament.demo_cleanup.failed'))
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('filament.demo_cleanup.deleted_all_success'))
                    ->body(__('filament.demo_cleanup.deleted_summary', [
                        'organizations' => number_format($summary->organizations),
                        'total' => number_format($summary->totalRecords()),
                    ]))
                    ->success()
                    ->send();
            });
    }

    public static function managementGroup(): ActionGroup
    {
        return ActionGroup::make([
            self::deleteAll(),
        ])
            ->label(__('filament.demo_cleanup.management_group'))
            ->icon(Heroicon::OutlinedBeaker)
            ->color('danger')
            ->button()
            ->visible(fn (): bool => auth()->user()?->role === UserRole::SuperAdmin);
    }

    public static function deleteRecord(): Action
    {
        return Action::make('deleteDemoData')
            ->label(__('filament.actions.delete_demo_data'))
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->visible(fn (Organization $record): bool => auth()->user()?->role === UserRole::SuperAdmin && $record->is_demo)
            ->requiresConfirmation()
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->modalIconColor('danger')
            ->modalHeading(__('filament.demo_cleanup.delete_one_heading'))
            ->modalDescription(fn (Organization $record): string => app(DemoOrganizationCleanupService::class)
                ->summarizeOrganization($record)
                ->toFilamentDescription())
            ->modalSubmitActionLabel(__('filament.actions.delete_demo_data'))
            ->modalCancelActionLabel(__('filament.actions.cancel'))
            ->action(function (Organization $record, \Livewire\Component $livewire): void {
                try {
                    $summary = app(DemoOrganizationCleanupService::class)->deleteOrganization($record);
                } catch (DemoCleanupException $exception) {
                    Notification::make()
                        ->title(__('filament.demo_cleanup.failed'))
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('filament.demo_cleanup.deleted_one_success', ['title' => $record->title]))
                    ->body(__('filament.demo_cleanup.deleted_summary', [
                        'organizations' => number_format($summary->organizations),
                        'total' => number_format($summary->totalRecords()),
                    ]))
                    ->success()
                    ->send();

                $livewire->redirect(OrganizationResource::getUrl('index'));
            });
    }
}

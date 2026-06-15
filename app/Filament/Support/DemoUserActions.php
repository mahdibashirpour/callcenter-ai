<?php

namespace App\Filament\Support;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Services\Demo\DemoEmployeeProvisioner;
use App\Support\Seeding\DemoCatalog;
use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

final class DemoUserActions
{
    public static function addEmployee(?Closure $resolveOrganization = null): Action
    {
        return Action::make('addDemoEmployee')
            ->label(__('filament.actions.add_demo_user'))
            ->icon(Heroicon::OutlinedUserPlus)
            ->color('primary')
            ->visible(function (mixed $record = null) use ($resolveOrganization): bool {
                if (auth()->user()?->role !== UserRole::SuperAdmin) {
                    return false;
                }

                $organization = self::resolveOrganization($record, $resolveOrganization);

                return $organization instanceof Organization && $organization->is_demo;
            })
            ->action(function (mixed $record = null) use ($resolveOrganization): void {
                $organization = self::resolveOrganization($record, $resolveOrganization);

                if (! $organization instanceof Organization) {
                    return;
                }

                $membership = app(DemoEmployeeProvisioner::class)->provision($organization);

                Notification::make()
                    ->title(__('filament.demo_users.added_success'))
                    ->body(__('filament.demo_users.added_body', [
                        'name' => $membership->full_name,
                        'email' => $membership->user->email,
                        'password' => DemoCatalog::DEMO_PASSWORD,
                    ]))
                    ->success()
                    ->send();
            });
    }

    private static function resolveOrganization(mixed $record, ?Closure $resolveOrganization): ?Organization
    {
        if ($resolveOrganization !== null) {
            $organization = $resolveOrganization();

            return $organization instanceof Organization ? $organization : null;
        }

        return $record instanceof Organization ? $record : null;
    }
}

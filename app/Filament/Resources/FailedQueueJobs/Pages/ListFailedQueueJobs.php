<?php

namespace App\Filament\Resources\FailedQueueJobs\Pages;

use App\Filament\Resources\FailedQueueJobs\FailedQueueJobResource;
use App\Models\FailedQueueJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListFailedQueueJobs extends ListRecords
{
    protected static string $resource = FailedQueueJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry_all')
                ->label(__('filament.actions.retry_all_failed_jobs'))
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->visible(fn (): bool => FailedQueueJob::query()->exists())
                ->action(function (): void {
                    Artisan::call('queue:retry', ['id' => 'all']);

                    Notification::make()
                        ->title(__('filament.notifications.all_failed_jobs_retried'))
                        ->success()
                        ->send();
                }),
            Action::make('flush_failed')
                ->label(__('filament.actions.flush_failed_jobs'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription(__('filament.misc.flush_failed_jobs_description'))
                ->visible(fn (): bool => FailedQueueJob::query()->exists())
                ->action(function (): void {
                    Artisan::call('queue:flush');

                    Notification::make()
                        ->title(__('filament.notifications.failed_jobs_flushed'))
                        ->success()
                        ->send();
                }),
        ];
    }
}

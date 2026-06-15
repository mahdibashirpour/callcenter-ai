<?php

namespace App\Application\Voip\Services;

use App\Application\Voip\Jobs\ProcessVoipIngestionJob;
use App\Application\Voip\Services\VoipConnectionResolver;
use App\Domain\Voip\Contracts\VoipPollableAdapterInterface;
use App\Domain\Voip\DTOs\NormalizedWebhookEvent;
use App\Domain\Voip\Enums\VoipEventSource;
use App\Domain\Voip\Enums\VoipIngestionMode;
use App\Domain\Voip\Enums\VoipLogStatus;
use App\Domain\Voip\Enums\VoipOperation;
use App\Domain\Voip\Contracts\VoipLogRepositoryInterface;
use App\Models\OrganizationVoipConnection;
use Illuminate\Support\Facades\Log;

class VoipPollingService
{
    public function __construct(
        private VoipConnectionResolver $resolver,
        private VoipLogRepositoryInterface $logs,
    ) {}

    public function pollDueConnections(): int
    {
        $queued = 0;

        OrganizationVoipConnection::query()
            ->with('provider')
            ->where('is_active', true)
            ->where('polling_enabled', true)
            ->each(function (OrganizationVoipConnection $connection) use (&$queued): void {
                if (! $this->isDue($connection)) {
                    return;
                }

                $queued += $this->pollConnection($connection);
            });

        return $queued;
    }

    public function pollConnection(OrganizationVoipConnection $connection): int
    {
        $connection->loadMissing('provider');

        if (! $this->connectionUsesPolling($connection)) {
            return 0;
        }

        try {
            [$config, $adapter] = $this->resolver->resolveByConnectionId($connection->id);

            if (! $adapter instanceof VoipPollableAdapterInterface) {
                Log::warning('VoIP connection configured for polling but adapter is not pollable', [
                    'connection_id' => $connection->id,
                    'adapter' => $adapter::class,
                ]);

                return 0;
            }

            $since = $connection->last_polled_at;
            $events = $adapter->pollCallEvents($since);
            $dispatched = 0;

            foreach ($events as $event) {
                $normalized = $event
                    ->withSource(VoipEventSource::Polling, $config->providerCode->value);

                ProcessVoipIngestionJob::dispatch(
                    $connection->id,
                    $normalized->toArray(),
                );
                $dispatched++;
            }

            $connection->update(['last_polled_at' => now()]);

            $this->logs->logSync(
                connectionId: $connection->id,
                operation: VoipOperation::SyncData,
                status: VoipLogStatus::Success,
                payload: ['events_found' => count($events), 'events_queued' => $dispatched],
                message: 'polling_cycle_completed',
                recordsProcessed: $dispatched,
            );

            return $dispatched;
        } catch (\Throwable $e) {
            $this->logs->logSync(
                connectionId: $connection->id,
                operation: VoipOperation::SyncData,
                status: VoipLogStatus::Failed,
                message: $e->getMessage(),
            );

            Log::error('VoIP polling failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    public function isDue(OrganizationVoipConnection $connection): bool
    {
        $interval = $this->pollingInterval($connection);

        if ($connection->last_polled_at === null) {
            return true;
        }

        return $connection->last_polled_at->lte(now()->subSeconds($interval));
    }

    public function pollingInterval(OrganizationVoipConnection $connection): int
    {
        $connection->loadMissing('provider');

        $interval = $connection->polling_interval_seconds
            ?? $connection->provider?->polling_interval_seconds
            ?? (int) config('voip.default_polling_interval_seconds', 30);

        return max(
            (int) config('voip.min_polling_interval_seconds', 10),
            min((int) config('voip.max_polling_interval_seconds', 60), $interval),
        );
    }

    public function connectionUsesPolling(OrganizationVoipConnection $connection): bool
    {
        $connection->loadMissing('provider');
        $mode = VoipIngestionMode::tryFrom($connection->ingestion_mode ?? '')
            ?? $this->defaultIngestionMode($connection);

        return $connection->polling_enabled && $mode->usesPolling();
    }

    private function defaultIngestionMode(OrganizationVoipConnection $connection): VoipIngestionMode
    {
        $supportsWebhook = (bool) ($connection->provider?->supports_webhook ?? true);
        $supportsPolling = (bool) ($connection->provider?->supports_polling ?? false);

        if ($supportsWebhook && $supportsPolling) {
            return VoipIngestionMode::Hybrid;
        }

        if ($supportsPolling) {
            return VoipIngestionMode::Polling;
        }

        return VoipIngestionMode::Webhook;
    }
}

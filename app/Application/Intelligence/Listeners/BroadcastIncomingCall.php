<?php

namespace App\Application\Intelligence\Listeners;

use App\Domain\Voip\Enums\CallDirection;
use App\Domain\Voip\Events\CallStarted;
use App\Models\VoipCallLog;
use App\Services\IncomingCallService;

class BroadcastIncomingCall
{
    public function __construct(private IncomingCallService $incomingCalls) {}

    public function handle(CallStarted $event): void
    {
        if ($event->event->direction !== CallDirection::Inbound) {
            return;
        }

        $callLog = VoipCallLog::query()
            ->where('organization_voip_connection_id', $event->connectionId)
            ->where('external_call_id', $event->event->callId)
            ->first();

        $this->incomingCalls->register([
            'organization_id' => $event->organizationId,
            'organization_voip_connection_id' => $event->connectionId,
            'voip_call_log_id' => $callLog?->id,
            'external_call_id' => $event->event->callId,
            'caller_number' => $event->event->sourceNumber ?? $event->event->destinationNumber ?? 'unknown',
            'customer_phone' => $event->event->sourceNumber,
            'direction' => 'inbound',
        ]);
    }
}

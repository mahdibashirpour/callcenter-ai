<?php

namespace App\Services;

use App\Domain\Call\Enums\IncomingCallStatus;
use App\Events\IncomingCallClaimed;
use App\Models\Call;
use App\Models\IncomingCallSession;
use Illuminate\Support\Facades\DB;

class CallClaimingService
{
    public function claim(int $sessionId, int $organizationUserId): IncomingCallSession
    {
        return DB::transaction(function () use ($sessionId, $organizationUserId) {
            $session = IncomingCallSession::query()
                ->with('organizationVoipConnection.provider')
                ->whereKey($sessionId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($session->status !== IncomingCallStatus::Ringing) {
                throw new \RuntimeException('This call has already been claimed or is no longer available.');
            }

            $session->update([
                'status' => IncomingCallStatus::Claimed,
                'claimed_by_organization_user_id' => $organizationUserId,
                'claimed_at' => now(),
            ]);

            $call = $this->ensureCallRecord($session, $organizationUserId);
            if ($call) {
                $session->update(['call_id' => $call->id]);
            }

            $session->load(['organization', 'claimedBy']);

            try {
                broadcast(new IncomingCallClaimed($session, $organizationUserId));
            } catch (\Throwable $e) {
                report($e);
            }

            return $session->fresh(['organization', 'claimedBy']);
        });
    }

    private function ensureCallRecord(IncomingCallSession $session, int $organizationUserId): ?Call
    {
        if ($session->call_id) {
            Call::query()->whereKey($session->call_id)->update([
                'organization_user_id' => $organizationUserId,
            ]);

            return Call::query()->find($session->call_id);
        }

        $providerCode = $session->organizationVoipConnection?->provider?->code
            ?? 'incoming';

        return Call::query()->create([
            'organization_id' => $session->organization_id,
            'organization_user_id' => $organizationUserId,
            'organization_voip_connection_id' => $session->organization_voip_connection_id,
            'voip_call_log_id' => $session->voip_call_log_id,
            'external_call_id' => $session->external_call_id ?? 'incoming-'.$session->id,
            'provider_code' => $providerCode,
            'source' => \App\Domain\Call\Enums\ConversationSource::Voip,
            'direction' => $session->direction,
            'caller_number' => $session->caller_number,
            'receiver_number' => $session->direction === 'inbound' ? 'main-line' : $session->customer_phone ?? 'unknown',
            'customer_name' => $session->customer_name,
            'customer_phone' => $session->customer_phone,
            'status' => 'active',
            'started_at' => $session->ring_started_at,
        ]);
    }
}

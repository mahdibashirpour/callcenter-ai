<?php

namespace App\Events;

use App\Models\IncomingCallSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingCallClaimed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public IncomingCallSession $session,
        public int $claimedByOrganizationUserId,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('organization.'.$this->session->organization_id.'.incoming-calls'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'IncomingCallClaimed';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'claimed_by_organization_user_id' => $this->claimedByOrganizationUserId,
            'claimed_by_name' => $this->session->claimedBy?->full_name,
        ];
    }
}

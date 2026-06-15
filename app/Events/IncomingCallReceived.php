<?php

namespace App\Events;

use App\Models\IncomingCallSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingCallReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public IncomingCallSession $session) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('organization.'.$this->session->organization_id.'.incoming-calls'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'IncomingCallReceived';
    }

    public function broadcastWith(): array
    {
        return $this->session->broadcastPayload();
    }
}

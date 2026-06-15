<?php

namespace App\Application\Voip\Jobs;

use App\Application\Voip\Services\VoipPollingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PollVoipConnectionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue((string) config('voip.queue', 'default'));
    }

    public function handle(VoipPollingService $polling): void
    {
        $polling->pollDueConnections();
    }
}

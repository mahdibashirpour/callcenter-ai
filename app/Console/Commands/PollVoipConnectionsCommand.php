<?php

namespace App\Console\Commands;

use App\Application\Voip\Services\VoipPollingService;
use Illuminate\Console\Command;

class PollVoipConnectionsCommand extends Command
{
    protected $signature = 'voip:poll';

    protected $description = 'Poll VoIP providers for call events on connections without webhooks';

    public function handle(VoipPollingService $polling): int
    {
        $queued = $polling->pollDueConnections();

        $this->components->info(sprintf('Queued %d VoIP event(s) from polling.', $queued));

        return self::SUCCESS;
    }
}

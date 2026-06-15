<?php

namespace App\Console\Commands;

use App\Services\ProcessingQueueFlusher;
use Illuminate\Queue\Console\FlushFailedCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'queue:flush')]
class SyncAwareFlushFailedQueueCommand extends FlushFailedCommand
{
    public function handle(): void
    {
        parent::handle();

        if ($this->isProhibited()) {
            return;
        }

        app(ProcessingQueueFlusher::class)->syncAfterQueueCommand('queue:flush');
    }
}

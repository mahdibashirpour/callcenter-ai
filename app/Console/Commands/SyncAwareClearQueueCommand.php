<?php

namespace App\Console\Commands;

use App\Services\ProcessingQueueFlusher;
use Illuminate\Queue\Console\ClearCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'queue:clear')]
class SyncAwareClearQueueCommand extends ClearCommand
{
    public function handle()
    {
        $exitCode = parent::handle();

        if ($exitCode === 0) {
            app(ProcessingQueueFlusher::class)->syncAfterQueueCommand('queue:clear');
        }

        return $exitCode;
    }
}

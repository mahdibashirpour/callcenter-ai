<?php

namespace App\Application\Voip\Jobs;

use App\Application\Voip\VoipManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncVoipExtensionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public ?int $connectionId = null,
    ) {}

    public function handle(): void
    {
        $manager = VoipManager::forOrganization($this->organizationId);

        if ($this->connectionId) {
            $manager->connection($this->connectionId);
        }

        $manager->getExtensions();
    }
}

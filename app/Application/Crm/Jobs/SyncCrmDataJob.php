<?php

namespace App\Application\Crm\Jobs;

use App\Application\Crm\CrmManager;
use App\Domain\Crm\DTOs\SyncData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCrmDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public ?int $connectionId = null,
        public array $syncData = [],
    ) {}

    public function handle(CrmManager $crmManager): void
    {
        $manager = CrmManager::forOrganization($this->organizationId);

        if ($this->connectionId) {
            $manager->connection($this->connectionId);
        }

        $manager->sync(SyncData::fromArray($this->syncData));
    }
}

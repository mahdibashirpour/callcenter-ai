<?php

namespace App\Domain\Crm\Events;

use App\Domain\Crm\Enums\CrmLogStatus;
use App\Domain\Crm\ValueObjects\CrmOperationResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrmSyncCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $connectionId,
        public CrmLogStatus $status,
        public CrmOperationResult $result,
        public ?int $recordsProcessed = null,
    ) {}
}

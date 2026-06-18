<?php

namespace App\Filament\Resources\PendingQueueJobs\Pages;

use App\Filament\Resources\PendingQueueJobs\PendingQueueJobResource;
use Filament\Resources\Pages\ListRecords;

class ListPendingQueueJobs extends ListRecords
{
    protected static string $resource = PendingQueueJobResource::class;
}

<?php

namespace App\Models;

use App\Services\QueueMonitoring\QueueJobInspection;
use App\Services\QueueMonitoring\QueueJobInspector;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class PendingQueueJob extends Model
{
    protected $table = 'jobs';

    public $timestamps = false;

    protected $guarded = [];

    public function inspection(): QueueJobInspection
    {
        return app(QueueJobInspector::class)->inspect($this->payload);
    }

    public function jobClassLabel(): string
    {
        return $this->inspection()->shortLabel();
    }

    public function callId(): ?int
    {
        return $this->inspection()->callId();
    }

    public function isReserved(): bool
    {
        return $this->reserved_at !== null;
    }

    public function queuedAt(): ?CarbonInterface
    {
        return $this->created_at
            ? \Illuminate\Support\Carbon::createFromTimestamp((int) $this->created_at)
            : null;
    }
}

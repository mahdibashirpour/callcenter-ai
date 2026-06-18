<?php

namespace App\Services\QueueMonitoring;

use Illuminate\Support\Facades\DB;

class QueueMonitorStats
{
    /** @return array{pending: int, reserved: int, failed: int, batches: int}> */
    public function overview(): array
    {
        $pendingQuery = DB::table('jobs');

        return [
            'pending' => (int) (clone $pendingQuery)->whereNull('reserved_at')->count(),
            'reserved' => (int) (clone $pendingQuery)->whereNotNull('reserved_at')->count(),
            'failed' => (int) DB::table('failed_jobs')->count(),
            'batches' => (int) DB::table('job_batches')->whereNull('finished_at')->count(),
        ];
    }
}

<?php

namespace App\Services;

use App\Application\Intelligence\Jobs\AnalyzeAudioJob;
use App\Domain\Recording\Contracts\RecordingRepositoryInterface;
use App\Models\CallProcessingJob;
use RuntimeException;

class ProcessingQueueJobService
{
    public function __construct(
        private CallProcessingTracker $tracker,
        private ProcessingQueueFlusher $flusher,
        private RecordingRepositoryInterface $recordings,
    ) {}

    public function retry(CallProcessingJob $job): CallProcessingJob
    {
        $this->assertRecoverable($job);

        $recording = $this->recordings->findByCallId($job->call_id);

        if ($recording?->status !== 'completed' || ! $recording->storagePath) {
            throw new RuntimeException(__('ui.processing.retry_missing_recording'));
        }

        $this->flusher->clearLaravelJobsForCall($job->call_id);

        $job = $this->tracker->requeueForAnalysis($job);

        AnalyzeAudioJob::dispatchChain($job->call_id);

        return $job->refresh();
    }

    public function delete(CallProcessingJob $job): void
    {
        $this->assertRecoverable($job);

        $this->flusher->clearLaravelJobsForCall($job->call_id);

        $job->delete();
    }

    private function assertRecoverable(CallProcessingJob $job): void
    {
        if (! $job->status->isRecoverable()) {
            throw new RuntimeException(__('ui.processing.action_not_allowed'));
        }
    }
}

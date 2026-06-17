<?php

namespace App\Livewire\Concerns;

use App\Models\CallProcessingJob;
use App\Services\ProcessingQueueJobService;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

trait ManagesProcessingQueueJob
{
    abstract protected function queueOrganizationId(): int;

    /** @return Builder<CallProcessingJob> */
    abstract protected function scopeProcessingJobs(Builder $query): Builder;

    protected function findQueueJob(int $jobId): CallProcessingJob
    {
        $job = $this->scopeProcessingJobs(
            CallProcessingJob::query()->whereKey($jobId),
        )->first();

        if (! $job || $job->organization_id !== $this->queueOrganizationId()) {
            abort(404);
        }

        return $job;
    }

    public function retryJob(int $jobId): void
    {
        try {
            app(ProcessingQueueJobService::class)->retry($this->findQueueJob($jobId));

            $this->dispatchQueueToast('success', __('ui.processing.retry_queued'));
        } catch (RuntimeException $exception) {
            $this->dispatchQueueToast('error', $exception->getMessage());
        }
    }

    public function deleteJob(int $jobId): void
    {
        try {
            app(ProcessingQueueJobService::class)->delete($this->findQueueJob($jobId));

            $this->dispatchQueueToast('success', __('ui.processing.deleted'));
        } catch (RuntimeException $exception) {
            $this->dispatchQueueToast('error', $exception->getMessage());
        }
    }

    protected function dispatchQueueToast(string $type, string $message): void
    {
        $detail = json_encode(
            ['type' => $type, 'message' => $message, 'url' => null],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP,
        );

        $this->js("window.dispatchEvent(new CustomEvent('show-toast', { detail: {$detail} }))");
    }
}

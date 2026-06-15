<?php

namespace App\Events;

use App\Models\CallProcessingJob;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallProcessingUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CallProcessingJob $job,
        public ?array $notification = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('organization.'.$this->job->organization_id.'.processing-queue'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'CallProcessingUpdated';
    }

    public function broadcastWith(): array
    {
        $this->job->loadMissing('call.latestAnalysis');

        return [
            'job' => [
                'id' => $this->job->id,
                'job_uuid' => $this->job->job_uuid,
                'call_id' => $this->job->call_id,
                'file_name' => $this->job->file_name,
                'status' => $this->job->status->value,
                'status_label' => $this->job->status->label(),
                'stage' => $this->job->stage->value,
                'stage_label' => $this->job->stage->label(),
                'progress_percentage' => $this->job->progress_percentage,
                'error_message' => $this->job->error_message,
                'upload_started_at' => $this->job->upload_started_at?->toIso8601String(),
                'processing_started_at' => $this->job->processing_started_at?->toIso8601String(),
                'completed_at' => $this->job->completed_at?->toIso8601String(),
                'has_analysis' => $this->job->call?->latestAnalysis !== null,
            ],
            'notification' => $this->notification,
        ];
    }
}

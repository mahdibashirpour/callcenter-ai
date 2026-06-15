<?php

namespace App\Models;

use App\Domain\Processing\Enums\ProcessingJobStage;
use App\Domain\Processing\Enums\ProcessingJobStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'job_uuid',
    'call_id',
    'organization_id',
    'organization_user_id',
    'uploader_id',
    'file_name',
    'status',
    'stage',
    'progress_percentage',
    'upload_started_at',
    'upload_completed_at',
    'queued_at',
    'processing_started_at',
    'completed_at',
    'waiting_seconds',
    'processing_duration_seconds',
    'error_message',
])]
class CallProcessingJob extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ProcessingJobStatus::class,
            'stage' => ProcessingJobStage::class,
            'upload_started_at' => 'datetime',
            'upload_completed_at' => 'datetime',
            'queued_at' => 'datetime',
            'processing_started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'organization_user_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CallProcessingLog::class)->orderBy('created_at');
    }

    public function isActive(): bool
    {
        return ! $this->status->isTerminal();
    }

    public function getRouteKeyName(): string
    {
        return 'job_uuid';
    }
}

<?php

namespace App\Models;

use App\Domain\Processing\Enums\ProcessingLogLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'call_processing_job_id',
    'call_id',
    'level',
    'source',
    'message',
    'context',
])]
class CallProcessingLog extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'level' => ProcessingLogLevel::class,
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(CallProcessingJob::class, 'call_processing_job_id');
    }

    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }
}

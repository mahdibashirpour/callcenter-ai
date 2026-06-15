<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id',
    'organization_user_id',
    'period_date',
    'analyses_count',
    'input_tokens',
    'output_tokens',
    'total_tokens',
    'total_cost',
    'total_processing_duration_ms',
    'average_score',
    'llm_provider',
    'model_name',
])]
class AiUsageDailySnapshot extends Model
{
    protected function casts(): array
    {
        return [
            'period_date' => 'date',
            'total_cost' => 'decimal:6',
            'average_score' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'organization_user_id');
    }
}

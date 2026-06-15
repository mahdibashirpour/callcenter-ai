<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'organization_user_id', 'period', 'period_start', 'period_end',
    'average_score', 'conversations_count', 'dimension_averages_json',
    'top_strengths_json', 'top_weaknesses_json',
])]
class EmployeePerformanceSnapshot extends Model
{
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'dimension_averages_json' => 'array',
            'top_strengths_json' => 'array',
            'top_weaknesses_json' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'organization_user_id');
    }
}

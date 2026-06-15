<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'organization_user_id',
    'integratable_type',
    'integratable_id',
    'key',
    'value',
])]
class EmployeeIntegrationMeta extends Model
{
    protected $table = 'employee_integration_meta';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'organization_user_id');
    }

    public function integratable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getConnectionLabelAttribute(): string
    {
        $integratable = $this->integratable;

        if (! $integratable) {
            return '—';
        }

        $type = class_basename($this->integratable_type);
        $provider = $integratable->provider?->name ?? $type;

        return "{$provider} · {$integratable->name}";
    }
}

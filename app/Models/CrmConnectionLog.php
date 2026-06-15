<?php

namespace App\Models;

use App\Domain\Crm\Enums\CrmLogStatus;
use App\Domain\Crm\Enums\CrmOperation;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_crm_connection_id',
    'operation',
    'status',
    'request_payload',
    'response_payload',
    'message',
])]
class CrmConnectionLog extends Model
{
    protected function casts(): array
    {
        return [
            'operation' => CrmOperation::class,
            'status' => CrmLogStatus::class,
            'request_payload' => 'array',
            'response_payload' => 'array',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(OrganizationCrmConnection::class, 'organization_crm_connection_id');
    }
}

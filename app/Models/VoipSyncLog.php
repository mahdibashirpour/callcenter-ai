<?php

namespace App\Models;

use App\Domain\Voip\Enums\VoipLogStatus;
use App\Domain\Voip\Enums\VoipOperation;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_voip_connection_id',
    'operation',
    'status',
    'payload',
    'message',
    'records_processed',
])]
class VoipSyncLog extends Model
{
    protected function casts(): array
    {
        return [
            'operation' => VoipOperation::class,
            'status' => VoipLogStatus::class,
            'payload' => 'array',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(OrganizationVoipConnection::class, 'organization_voip_connection_id');
    }
}

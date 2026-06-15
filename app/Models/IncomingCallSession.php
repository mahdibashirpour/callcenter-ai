<?php

namespace App\Models;

use App\Domain\Call\Enums\IncomingCallStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id',
    'organization_voip_connection_id',
    'voip_call_log_id',
    'call_id',
    'external_call_id',
    'caller_number',
    'customer_name',
    'customer_phone',
    'direction',
    'status',
    'claimed_by_organization_user_id',
    'claimed_at',
    'customer_context_json',
    'recommended_actions_json',
    'recent_actions_json',
    'customer_timeline_json',
    'ring_started_at',
    'expires_at',
])]
class IncomingCallSession extends Model
{
    protected function casts(): array
    {
        return [
            'status' => IncomingCallStatus::class,
            'customer_context_json' => 'array',
            'recommended_actions_json' => 'array',
            'recent_actions_json' => 'array',
            'customer_timeline_json' => 'array',
            'claimed_at' => 'datetime',
            'ring_started_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizationVoipConnection(): BelongsTo
    {
        return $this->belongsTo(OrganizationVoipConnection::class, 'organization_voip_connection_id');
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'claimed_by_organization_user_id');
    }

    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    public function isRinging(): bool
    {
        return $this->status === IncomingCallStatus::Ringing;
    }

    public function broadcastPayload(): array
    {
        return [
            'session_id' => $this->id,
            'caller_number' => $this->caller_number,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone ?? $this->caller_number,
            'organization_id' => $this->organization_id,
            'organization_name' => $this->organization?->title,
            'ring_started_at' => $this->ring_started_at?->toIso8601String(),
            'direction' => $this->direction,
        ];
    }

    public function intelligencePayload(): array
    {
        return [
            'session_id' => $this->id,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone ?? $this->caller_number,
            'last_contact_date' => $this->customer_context_json['last_contact_date'] ?? null,
            'context_summary' => $this->customer_context_json['summary'] ?? null,
            'recommended_actions' => $this->recommended_actions_json ?? [],
            'recent_actions' => $this->recent_actions_json ?? [],
            'timeline' => $this->customer_timeline_json ?? [],
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'pipeline_execution_id', 'conversation_analysis_id',
    'organization_crm_connection_id', 'provider_code', 'action_type', 'status',
    'external_id', 'payload', 'error_message',
])]
class CrmPipelineSync extends Model
{
    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(ConversationAnalysis::class, 'conversation_analysis_id');
    }

    public function crmConnection(): BelongsTo
    {
        return $this->belongsTo(OrganizationCrmConnection::class, 'organization_crm_connection_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'organization_id',
    'voip_provider_id',
    'name',
    'credentials',
    'settings',
    'is_default',
    'is_active',
    'ingestion_mode',
    'polling_enabled',
    'polling_interval_seconds',
    'last_polled_at',
])]
class OrganizationVoipConnection extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationVoipConnectionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'settings' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'polling_enabled' => 'boolean',
            'polling_interval_seconds' => 'integer',
            'last_polled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (OrganizationVoipConnection $connection): void {
            if ($connection->is_default) {
                static::query()
                    ->where('organization_id', $connection->organization_id)
                    ->whereKeyNot($connection->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(VoipProvider::class, 'voip_provider_id');
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(VoipCallLog::class);
    }

    public function webhookLogs(): HasMany
    {
        return $this->hasMany(VoipWebhookLog::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(VoipSyncLog::class);
    }
}

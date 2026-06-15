<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['name', 'code', 'is_active', 'config'])]
class CrmProvider extends Model
{
    /** @use HasFactory<\Database\Factories\CrmProviderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'config' => 'array',
        ];
    }

    public function connections(): HasMany
    {
        return $this->hasMany(OrganizationCrmConnection::class);
    }

    public function metaDefinitions(): MorphMany
    {
        return $this->morphMany(IntegrationMetaDefinition::class, 'provider');
    }
}

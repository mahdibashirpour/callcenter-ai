<?php

namespace App\Models;

use App\Enums\IntegrationMetaFieldType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'provider_type',
    'provider_id',
    'name',
    'key',
    'field_type',
    'is_required',
    'placeholder',
    'help_text',
    'sort_order',
])]
class IntegrationMetaDefinition extends Model
{
    protected function casts(): array
    {
        return [
            'field_type' => IntegrationMetaFieldType::class,
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function provider(): MorphTo
    {
        return $this->morphTo();
    }
}

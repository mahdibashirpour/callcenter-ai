<?php

namespace App\Models;

use App\Domain\Billing\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'organization_id',
    'type',
    'amount',
    'balance_before',
    'balance_after',
    'reference_type',
    'reference_id',
    'description',
])]
class WalletTransaction extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'type' => WalletTransactionType::class,
            'amount' => 'decimal:6',
            'balance_before' => 'decimal:6',
            'balance_after' => 'decimal:6',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (WalletTransaction $transaction) {
            $transaction->created_at ??= now();
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}

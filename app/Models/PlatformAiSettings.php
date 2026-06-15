<?php

namespace App\Models;

use App\Domain\Billing\Enums\ConversationEstimateType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

#[Fillable([
    'default_llm_provider_id',
    'default_llm_model_id',
    'allow_negative_balance',
    'currency',
    'estimation_words_per_minute',
    'estimation_tokens_per_word',
    'estimation_conversation_ratios',
])]
class PlatformAiSettings extends Model
{
    protected function casts(): array
    {
        return [
            'allow_negative_balance' => 'boolean',
            'estimation_tokens_per_word' => 'decimal:2',
            'estimation_conversation_ratios' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'currency' => 'IRR',
            'allow_negative_balance' => false,
            'estimation_words_per_minute' => 150,
            'estimation_tokens_per_word' => 1.30,
            'estimation_conversation_ratios' => self::defaultConversationRatios(),
        ]);
    }

    /** @return array<string, float> */
    public static function defaultConversationRatios(): array
    {
        return collect(ConversationEstimateType::cases())
            ->mapWithKeys(fn (ConversationEstimateType $type) => [
                $type->value => $type->defaultOutputRatio(),
            ])
            ->all();
    }

    public function conversationRatio(ConversationEstimateType $type): float
    {
        $ratios = $this->estimation_conversation_ratios ?? self::defaultConversationRatios();

        return (float) ($ratios[$type->value] ?? $type->defaultOutputRatio());
    }

    public static function currencyCode(): string
    {
        return static::current()->currency ?? 'IRR';
    }

    public static function formatMoney(float|int $amount): string
    {
        return Number::currency($amount, static::currencyCode(), 'fa');
    }

    public function defaultProvider(): BelongsTo
    {
        return $this->belongsTo(LlmProvider::class, 'default_llm_provider_id');
    }

    public function defaultModel(): BelongsTo
    {
        return $this->belongsTo(LlmModel::class, 'default_llm_model_id');
    }
}

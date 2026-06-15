<?php

namespace App\Models;

use App\Domain\Llm\Enums\LlmLogStatus;
use App\Domain\Llm\Enums\LlmOperation;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'operation',
    'status',
    'request_payload',
    'response_payload',
    'message',
    'input_tokens',
    'output_tokens',
    'cost',
    'duration_ms',
])]
class LlmAnalysisLog extends Model
{
    protected function casts(): array
    {
        return [
            'operation' => LlmOperation::class,
            'status' => LlmLogStatus::class,
            'request_payload' => 'array',
            'response_payload' => 'array',
            'cost' => 'decimal:6',
        ];
    }
}

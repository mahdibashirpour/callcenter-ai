<?php

namespace App\Domain\Llm\Enums;

enum LlmProviderCode: string
{
    case OpenAi = 'openai';
    case Anthropic = 'anthropic';
    case Gemini = 'gemini';
    case OpenRouter = 'openrouter';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::OpenAi => 'OpenAI',
            self::Anthropic => 'Anthropic',
            self::Gemini => 'Google Gemini',
            self::OpenRouter => 'OpenRouter',
            self::Custom => 'ارائه‌دهنده سفارشی',
        };
    }
}

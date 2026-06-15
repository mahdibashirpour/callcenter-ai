<?php

namespace App\Infrastructure\Llm\Adapters;

use App\Domain\Llm\Enums\LlmProviderCode;

class OpenRouterProvider extends OpenAiProvider
{
    public function getProviderCode(): LlmProviderCode
    {
        return LlmProviderCode::OpenRouter;
    }

    public function supportedModels(): array
    {
        return ['openai/gpt-4o', 'openai/gpt-4o-mini', 'anthropic/claude-3.5-sonnet', 'google/gemini-pro-1.5'];
    }

    protected function resolveBaseUrl(): string
    {
        return $this->config->credentials->baseUrl ?? 'https://openrouter.ai/api/v1';
    }
}

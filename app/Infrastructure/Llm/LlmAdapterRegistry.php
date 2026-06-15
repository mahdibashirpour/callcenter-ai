<?php

namespace App\Infrastructure\Llm;

use App\Domain\Llm\Contracts\LlmProviderInterface;
use App\Domain\Llm\Enums\LlmProviderCode;
use App\Domain\Llm\Exceptions\LlmProviderNotFoundException;
use App\Infrastructure\Llm\Adapters\AnthropicProvider;
use App\Infrastructure\Llm\Adapters\CustomLlmProvider;
use App\Infrastructure\Llm\Adapters\GeminiProvider;
use App\Infrastructure\Llm\Adapters\OpenAiProvider;
use App\Infrastructure\Llm\Adapters\OpenRouterProvider;

class LlmAdapterRegistry
{
    /** @var array<string, class-string<LlmProviderInterface>> */
    private array $providers = [];

    public function __construct()
    {
        $this->register(LlmProviderCode::OpenAi, OpenAiProvider::class);
        $this->register(LlmProviderCode::Anthropic, AnthropicProvider::class);
        $this->register(LlmProviderCode::Gemini, GeminiProvider::class);
        $this->register(LlmProviderCode::OpenRouter, OpenRouterProvider::class);
        $this->register(LlmProviderCode::Custom, CustomLlmProvider::class);
    }

    public function register(LlmProviderCode|string $provider, string $adapterClass): void
    {
        $code = $provider instanceof LlmProviderCode ? $provider->value : $provider;
        $this->providers[$code] = $adapterClass;
    }

    public function resolve(LlmProviderCode|string $provider): LlmProviderInterface
    {
        $code = $provider instanceof LlmProviderCode ? $provider->value : $provider;

        if (! isset($this->providers[$code])) {
            throw LlmProviderNotFoundException::forProvider($code);
        }

        return app($this->providers[$code]);
    }
}

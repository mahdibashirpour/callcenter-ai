<?php

namespace App\Infrastructure\Llm\Adapters;

use App\Domain\Llm\DTOs\AudioAnalysisRequestData;
use App\Domain\Llm\Enums\LlmProviderCode;
use App\Domain\Llm\ValueObjects\LlmOperationResult;
use Illuminate\Support\Facades\Http;

class GeminiProvider extends AbstractLlmProvider
{
    public function getProviderCode(): LlmProviderCode
    {
        return LlmProviderCode::Gemini;
    }

    public function supportedModels(): array
    {
        return ['gemini-2.0-flash', 'gemini-1.5-pro', 'gemini-1.5-flash'];
    }

    public function testConnection(): LlmOperationResult
    {
        if (! $this->hasApiKey()) {
            return LlmOperationResult::success(message: 'Gemini configured in demo mode (no API key).');
        }

        $model = $this->resolveModel(null, 'gemini-2.0-flash');
        $baseUrl = $this->config->credentials->baseUrl ?? 'https://generativelanguage.googleapis.com/v1beta';
        $response = Http::get("{$baseUrl}/models/{$model}", ['key' => $this->config->credentials->apiKey]);

        return $response->successful()
            ? LlmOperationResult::success(message: 'Gemini connection successful.')
            : $this->failure('Gemini connection failed.');
    }

    public function analyzeAudio(AudioAnalysisRequestData $request): LlmOperationResult
    {
        $model = $this->resolveModel($request->model, 'gemini-2.0-flash');

        return $this->demoAudioAnalysis($request, $model);
    }
}

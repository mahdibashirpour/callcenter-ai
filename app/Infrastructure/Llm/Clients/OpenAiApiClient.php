<?php

namespace App\Infrastructure\Llm\Clients;

use App\Domain\Llm\ValueObjects\LlmOperationResult;
use Illuminate\Support\Facades\Http;

class OpenAiApiClient
{
    public function __construct(
        private string $apiKey,
        private ?string $baseUrl = null,
    ) {}

    public function testConnection(): LlmOperationResult
    {
        $response = Http::withToken($this->apiKey)
            ->get(($this->baseUrl ?? 'https://api.openai.com/v1').'/models');

        return $response->successful()
            ? LlmOperationResult::success(message: 'OpenAI connection successful.')
            : LlmOperationResult::failure('OpenAI connection failed: '.$response->body());
    }

    public function transcribe(string $recordingUrl, string $model, ?string $language): LlmOperationResult
    {
        $audioResponse = Http::timeout(60)->get($recordingUrl);

        if (! $audioResponse->successful()) {
            return LlmOperationResult::failure('Unable to fetch recording for transcription.');
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(120)
            ->attach('file', $audioResponse->body(), 'recording.mp3')
            ->post(($this->baseUrl ?? 'https://api.openai.com/v1').'/audio/transcriptions', [
                'model' => $model,
                'language' => $language,
                'response_format' => 'json',
            ]);

        if (! $response->successful()) {
            return LlmOperationResult::failure('OpenAI transcription failed: '.$response->body());
        }

        $body = $response->json();

        return LlmOperationResult::success(
            data: [
                'transcript' => $body['text'] ?? '',
                'language' => $language ?? $body['language'] ?? null,
            ],
        );
    }
}

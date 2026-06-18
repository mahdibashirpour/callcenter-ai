<?php

namespace Tests\Unit;

use App\Domain\Llm\DTOs\AudioAnalysisRequestData;
use App\Domain\Llm\DTOs\LlmConnectionConfig;
use App\Domain\Llm\DTOs\LlmCredentials;
use App\Domain\Llm\DTOs\LlmSettings;
use App\Domain\Llm\Enums\LlmProviderCode;
use App\Infrastructure\Llm\Adapters\OpenAiProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAiProviderAudioAnalysisTest extends TestCase
{
    use RefreshDatabase;
    public function test_refuses_demo_analysis_when_real_audio_exists_without_api_key(): void
    {
        $provider = new OpenAiProvider;
        $provider->configure(new LlmConnectionConfig(
            connectionId: 1,
            organizationId: 1,
            providerCode: LlmProviderCode::OpenAi,
            name: 'OpenAI',
            credentials: new LlmCredentials(apiKey: null),
            settings: new LlmSettings(defaultModel: 'gpt-5'),
            isDefault: true,
            isActive: true,
        ));

        $result = $provider->analyzeAudio(new AudioAnalysisRequestData(
            callId: 1,
            storagePath: 'recordings/1/sample.mp3',
            storageDisk: 'local',
        ));

        $this->assertFalse($result->success);
        $this->assertStringContainsString('کلید API', $result->error ?? '');
    }

    public function test_keeps_intermediary_model_key_when_custom_base_url_is_configured(): void
    {
        $analysisJson = json_encode([
            'score' => 80,
            'summary' => 'خلاصه',
            'sentiment' => 'positive',
            'strengths' => [],
            'weaknesses' => [],
            'next_actions' => [],
        ], JSON_UNESCAPED_UNICODE);

        Http::fake([
            'https://example.com/recording.mp3' => Http::response('audio-bytes'),
            'https://api.avalai.ir/*' => Http::response([
                'choices' => [[
                    'message' => ['content' => $analysisJson],
                ]],
                'usage' => ['prompt_tokens' => 10, 'completion_tokens' => 5],
            ]),
        ]);

        $provider = new OpenAiProvider;
        $provider->configure(new LlmConnectionConfig(
            connectionId: 1,
            organizationId: 1,
            providerCode: LlmProviderCode::OpenAi,
            name: 'AvalAI',
            credentials: new LlmCredentials(
                apiKey: 'test-key',
                baseUrl: 'https://api.avalai.ir/v1',
            ),
            settings: new LlmSettings(),
            isDefault: true,
            isActive: true,
        ));

        $result = $provider->analyzeAudio(new AudioAnalysisRequestData(
            callId: 1,
            recordingUrl: 'https://example.com/recording.mp3',
            model: 'google/gemini-3.1-flash-lite',
            sendAudioFile: true,
            mimeType: 'audio/mpeg',
        ));

        $this->assertTrue($result->success);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.avalai.ir')
                && ($request->data()['model'] ?? null) === 'google/gemini-3.1-flash-lite';
        });
    }
}

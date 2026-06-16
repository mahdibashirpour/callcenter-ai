<?php

namespace Tests\Unit;

use App\Domain\Llm\DTOs\AudioAnalysisRequestData;
use App\Domain\Llm\DTOs\LlmConnectionConfig;
use App\Domain\Llm\DTOs\LlmCredentials;
use App\Domain\Llm\DTOs\LlmSettings;
use App\Domain\Llm\Enums\LlmProviderCode;
use App\Infrastructure\Llm\Adapters\OpenAiProvider;
use Tests\TestCase;

class OpenAiProviderAudioAnalysisTest extends TestCase
{
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
}

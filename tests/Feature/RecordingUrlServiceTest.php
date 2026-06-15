<?php

namespace Tests\Feature;

use App\Models\CallRecording;
use App\Services\RecordingUrlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecordingUrlServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_private_recording_uses_signed_playback_url(): void
    {
        config(['recordings.disk' => 'local']);

        $path = 'recordings/test-'.uniqid().'/sample.mp3';
        Storage::disk('local')->put($path, 'audio-bytes');

        $recording = new CallRecording([
            'storage_disk' => 'local',
            'storage_path' => $path,
            'mime_type' => 'audio/mpeg',
        ]);

        $url = app(RecordingUrlService::class)->resolve($recording);

        $this->assertNotNull($url);
        $this->assertStringContainsString('signature=', $url);
        $this->assertStringContainsString($path, $url);

        Storage::disk('local')->delete($path);
    }

    public function test_missing_recording_falls_back_to_source_url(): void
    {
        Storage::fake('local');

        $recording = new CallRecording([
            'storage_disk' => 'local',
            'storage_path' => 'recordings/1/missing.mp3',
        ]);

        $url = app(RecordingUrlService::class)->resolve($recording, 'https://example.com/fallback.mp3');

        $this->assertSame('https://example.com/fallback.mp3', $url);
    }
}

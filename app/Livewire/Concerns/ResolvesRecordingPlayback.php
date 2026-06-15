<?php

namespace App\Livewire\Concerns;

use App\Models\CallRecording;
use App\Services\RecordingRetentionService;

trait ResolvesRecordingPlayback
{
    /** @return array{url: ?string, expired: bool} */
    protected function recordingPlaybackState(?CallRecording $recording, ?string $fallbackUrl = null): array
    {
        return app(RecordingRetentionService::class)->playbackState($recording, $fallbackUrl);
    }

    protected function recordingPlaybackUrl(?CallRecording $recording, ?string $fallbackUrl = null): ?string
    {
        return $this->recordingPlaybackState($recording, $fallbackUrl)['url'];
    }
}

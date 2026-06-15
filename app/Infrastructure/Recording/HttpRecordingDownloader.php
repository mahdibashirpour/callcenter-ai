<?php

namespace App\Infrastructure\Recording;

use App\Domain\Recording\Contracts\RecordingDownloaderInterface;
use App\Domain\Recording\ValueObjects\RecordingDownloadResult;
use App\Services\RecordingStorage;
use Illuminate\Support\Facades\Http;

class HttpRecordingDownloader implements RecordingDownloaderInterface
{
    public function __construct(
        private RecordingStorage $recordingStorage,
    ) {}

    public function download(string $url, int $callId): RecordingDownloadResult
    {
        try {
            $response = Http::timeout(120)->get($url);

            if (! $response->successful()) {
                return new RecordingDownloadResult(success: false, error: 'Failed to download recording.');
            }

            $path = "recordings/{$callId}/".now()->format('YmdHis').'.mp3';
            $mimeType = $response->header('Content-Type') ?? 'audio/mpeg';
            $this->recordingStorage->put($path, $response->body(), $mimeType);

            return new RecordingDownloadResult(
                success: true,
                storagePath: $path,
                storageDisk: $this->recordingStorage->disk(),
                mimeType: $mimeType,
                fileSizeBytes: strlen($response->body()),
            );
        } catch (\Throwable $e) {
            return new RecordingDownloadResult(success: false, error: $e->getMessage());
        }
    }
}

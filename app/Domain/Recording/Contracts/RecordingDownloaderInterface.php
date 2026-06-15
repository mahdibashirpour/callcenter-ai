<?php

namespace App\Domain\Recording\Contracts;

use App\Domain\Recording\ValueObjects\RecordingDownloadResult;

interface RecordingDownloaderInterface
{
    public function download(string $url, int $callId): RecordingDownloadResult;
}

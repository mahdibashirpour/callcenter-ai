<?php

namespace App\Domain\Recording\ValueObjects;

readonly class RecordingDownloadResult
{
    public function __construct(
        public bool $success,
        public ?string $storagePath = null,
        public ?string $storageDisk = null,
        public ?string $mimeType = null,
        public ?int $fileSizeBytes = null,
        public ?string $error = null,
    ) {}
}

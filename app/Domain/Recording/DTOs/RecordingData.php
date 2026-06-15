<?php

namespace App\Domain\Recording\DTOs;

readonly class RecordingData
{
    public function __construct(
        public int $callId,
        public ?string $sourceUrl = null,
        public ?string $storageDisk = 'local',
        public ?string $storagePath = null,
        public ?string $mimeType = null,
        public ?int $fileSizeBytes = null,
        public ?int $durationSeconds = null,
        public string $channels = 'mono',
        public string $status = 'pending',
        public ?int $id = null,
    ) {}
}

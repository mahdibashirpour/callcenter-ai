<?php

namespace App\Domain\Llm\DTOs;

readonly class AudioAnalysisRequestData
{
    public function __construct(
        public int $callId,
        public ?string $storagePath = null,
        public ?string $storageDisk = null,
        public ?string $recordingUrl = null,
        public ?string $mimeType = null,
        public ?string $model = null,
        public ?string $promptVersion = null,
        public ?PromptContextData $context = null,
        public ?int $organizationId = null,
        public ?int $organizationUserId = null,
        public ?int $voipCallLogId = null,
        public bool $sendAudioFile = true,
        public ?string $playbackUrl = null,
    ) {}
}

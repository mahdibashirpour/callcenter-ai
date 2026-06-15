<?php

namespace App\Domain\Recording\Contracts;

use App\Domain\Recording\DTOs\RecordingData;

interface RecordingRepositoryInterface
{
    public function create(RecordingData $data): int;

    public function update(int $recordingId, RecordingData $data): void;

    public function findByCallId(int $callId): ?RecordingData;
}

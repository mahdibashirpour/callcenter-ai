<?php

namespace App\Application\Voip\Services;

use App\Domain\Voip\DTOs\NormalizedWebhookEvent;
use App\Domain\Voip\Enums\CallStatus;
use App\Domain\Voip\Enums\VoipWebhookEventType;
use App\Models\VoipCallLog;

class VoipEventDeduplicator
{
    public function isDuplicate(NormalizedWebhookEvent $event, ?VoipCallLog $existing): bool
    {
        if (! $existing || ! $event->callId) {
            return false;
        }

        return match ($event->type) {
            VoipWebhookEventType::CallStarted => $existing->started_at !== null
                && $existing->status === CallStatus::Ringing,
            VoipWebhookEventType::CallAnswered => $existing->status === CallStatus::Answered,
            VoipWebhookEventType::CallEnded => $existing->ended_at !== null
                && $existing->status === CallStatus::Completed
                && ($event->recordingUrl === null || $existing->recording_url === $event->recordingUrl),
            VoipWebhookEventType::CallMissed => $existing->status === CallStatus::Missed,
            VoipWebhookEventType::RecordingCreated => $existing->recording_url !== null
                && ($event->recordingUrl === null || $existing->recording_url === $event->recordingUrl),
            default => false,
        };
    }
}

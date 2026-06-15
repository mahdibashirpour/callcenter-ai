<?php

namespace App\Services;

use App\Models\Call;
use App\Models\ConversationAnalysis;

class CustomerPhoneResolver
{
    public function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        return $digits !== '' ? $digits : null;
    }

    public function resolveFromCall(Call $call): ?string
    {
        $candidates = match ($call->direction) {
            'inbound' => [$call->caller_number, $call->customer_phone],
            'outbound' => [$call->receiver_number, $call->customer_phone],
            default => [$call->customer_phone, $call->caller_number, $call->receiver_number],
        };

        foreach ($candidates as $candidate) {
            if ($this->normalize($candidate)) {
                return trim((string) $candidate);
            }
        }

        return null;
    }

    public function resolveFromAnalysis(ConversationAnalysis $analysis): ?string
    {
        $call = $analysis->call;
        if ($call) {
            return $this->resolveFromCall($call);
        }

        $identityPhone = trim((string) ($analysis->customer_identity_json['phone_number'] ?? ''));

        return $identityPhone !== '' ? $identityPhone : null;
    }
}

<?php

namespace App\Http\Controllers;

use App\Application\Voip\Actions\HandleVoipWebhookAction;
use App\Application\Voip\Jobs\ProcessVoipWebhookJob;
use App\Models\OrganizationVoipConnection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoipWebhookController extends Controller
{
    public function __invoke(Request $request, int $connectionId, HandleVoipWebhookAction $action): JsonResponse
    {
        $connection = OrganizationVoipConnection::query()->findOrFail($connectionId);

        $secret = $connection->settings['webhook_secret'] ?? null;
        if ($secret && $request->header('X-Voip-Webhook-Secret') !== $secret) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        ProcessVoipWebhookJob::dispatch($connectionId, $request->all());

        return response()->json(['message' => 'Webhook accepted'], 202);
    }
}

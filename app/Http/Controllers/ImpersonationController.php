<?php

namespace App\Http\Controllers;

use App\Application\Impersonation\Actions\StopImpersonationAction;
use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function stop(Request $request, StopImpersonationAction $stop): RedirectResponse
    {
        if (! app(ImpersonationService::class)->isImpersonating()) {
            abort(403, 'No active impersonation session.');
        }

        $redirectUrl = $stop->execute();

        return redirect($redirectUrl);
    }
}

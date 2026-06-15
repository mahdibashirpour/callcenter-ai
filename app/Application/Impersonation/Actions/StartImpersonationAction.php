<?php

namespace App\Application\Impersonation\Actions;

use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StartImpersonationAction
{
    public function __construct(private ImpersonationService $impersonation) {}

    public function execute(User $admin, User $target, Request $request): string
    {
        $this->impersonation->start($admin, $target, $request);

        Auth::login($target);

        return $target->portalRoute();
    }
}

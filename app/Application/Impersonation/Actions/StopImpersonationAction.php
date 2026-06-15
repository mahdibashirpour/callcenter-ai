<?php

namespace App\Application\Impersonation\Actions;

use App\Services\ImpersonationService;
use Illuminate\Support\Facades\Auth;

class StopImpersonationAction
{
    public function __construct(private ImpersonationService $impersonation) {}

    public function execute(): string
    {
        $admin = $this->impersonation->stop();

        Auth::login($admin);

        return url('/admin');
    }
}

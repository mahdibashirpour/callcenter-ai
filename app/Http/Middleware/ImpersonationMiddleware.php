<?php

namespace App\Http\Middleware;

use App\Services\ImpersonationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    public function __construct(private ImpersonationService $impersonation) {}

    public function handle(Request $request, Closure $next): Response
    {
        View::share('impersonationContext', $this->impersonation->context());

        return $next($request);
    }
}

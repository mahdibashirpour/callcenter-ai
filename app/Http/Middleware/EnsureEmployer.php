<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::Employer || ! $user->organizations()->exists()) {
            abort(403, 'دسترسی کارفرما الزامی است.');
        }

        return $next($request);
    }
}

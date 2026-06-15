<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::Employee || ! $user->employeeOrganizations()->exists()) {
            abort(403, 'دسترسی کارشناس الزامی است.');
        }

        return $next($request);
    }
}

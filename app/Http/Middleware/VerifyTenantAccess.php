<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyTenantAccess extends Middleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Super admins can access everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Get company ID from route or session
        $companyId = $request->route('company_id') ?? $user->company_id;

        // Verify user belongs to the company
        if ($companyId && $user->company_id !== $companyId) {
            abort(403, 'Unauthorized access to this company.');
        }

        // Store company context for query scoping
        $request->attributes->set('company_id', $user->company_id);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange extends Middleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Skip for certain routes
        if (in_array($request->route()->getName(), [
            'password.force-change',
            'password.update-forced',
            'logout',
        ])) {
            return $next($request);
        }

        // If user needs to change password, redirect them
        if ($user->force_password_change) {
            return redirect()->route('password.force-change');
        }

        return $next($request);
    }
}

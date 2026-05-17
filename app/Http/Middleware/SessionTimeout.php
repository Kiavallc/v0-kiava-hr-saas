<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginSession;

class SessionTimeout extends Middleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $timeoutMinutes = (int) config('session.timeout_minutes', 120);

        // Get or create session record
        $session = LoginSession::where('user_id', $user->id)
            ->where('session_token', session()->getId())
            ->first();

        if (!$session) {
            LoginSession::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'session_token' => session()->getId(),
                'last_activity_at' => now(),
                'expires_at' => now()->addMinutes($timeoutMinutes),
            ]);
        } else {
            // Check if session has expired
            if ($session->expires_at->isPast()) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
            }

            // Update last activity
            $session->update([
                'last_activity_at' => now(),
                'expires_at' => now()->addMinutes($timeoutMinutes),
            ]);
        }

        return $next($request);
    }
}

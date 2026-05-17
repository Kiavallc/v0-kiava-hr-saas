<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetTenantContext
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->company_id) {
            // Set global company context for all queries
            \Illuminate\Support\Facades\DB::connection()->getPdo()
                ->prepare("SET app.current_company_id = ?")
                ->execute([$request->user()->company_id]);

            // Store in request for easy access
            $request->attributes->set('company_id', $request->user()->company_id);
        }

        return $next($request);
    }
}

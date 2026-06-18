<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgencyApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $agency = $request->user()?->agency;

        if ($agency === null || $agency->status !== 'active') {
            return redirect()->route('agency.pending');
        }

        return $next($request);
    }
}

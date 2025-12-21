<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenants
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()->tybe !== 'tenant')
        {
            return response()->json(['message'=>'only tenants can perform this action '], 403);
        }
        return $next($request);
    }
}

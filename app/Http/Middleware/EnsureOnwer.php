<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnwer
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()->tybe !== 'owner')
        {
            return response()->json(['message'=>'only owners can perform this action'], 403);
        }
        return $next($request);
    }
}

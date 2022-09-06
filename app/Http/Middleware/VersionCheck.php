<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VersionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (
            !$request->hasHeader('X-Inertia')
            && $request->hasHeader('X-App-Version')
            && $request->header('X-App-Version') !== config('app.version')
        ) {
            return response()->json(['error' => 'client version has expired'], Response::HTTP_CONFLICT);
        }

        return $next($request);
    }
}

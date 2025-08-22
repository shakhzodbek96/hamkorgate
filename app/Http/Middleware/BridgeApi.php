<?php

namespace App\Http\Middleware;

use App\Services\Helpers\Check;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class BridgeApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $provided = $request->bearerToken();
        $current = Cache::get('bridge_token');

        if (! $provided || $provided !== $current) {
            return response()->json(['message'=>'Invalid or missing token!'], 403);
        }

        return $next($request);
    }
}

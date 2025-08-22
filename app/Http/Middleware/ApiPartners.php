<?php

namespace App\Http\Middleware;

use App\Models\Partner;
use App\Services\Helpers\LogWriter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiPartners
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request_start = microtime(true);
        $token = $request->bearerToken();
        if (strlen($token) < 32)
            return response()->json([
                'status' => false,
                'error' => [
                    'message' => 'Invalid token!'
                ]
            ],401);

        $partner = Cache::remember('partner_token_'.$token, 7200, function () use ($token) {
            $p = Partner::where('config->auth->token', $token)->first();
            return $p ? $p->toArray() : null;
        });

        if (!$partner)
            return response()->json([
                'status' => false,
                'error' => [
                    'message' => 'Invalid token!'
                ]
            ],401);

        if ($partner['id'])
        {
            // Rate limit
            $currentMinute = date('i');
            $retryAfter = 60 - date('s');
            $cacheKey = 'rate_limit_partners_'.$partner['id']."_$currentMinute";
            $reqCount = Cache::get($cacheKey, 0);
            $config = $partner['config'] ?? [];
            $rate_limit = $config['auth']['rate_limit'] ?? 200;
            if ($reqCount > $rate_limit)
                return response()->json([
                    'status' => false,
                    'error' => [
                        'message' => 'Rate limit exceeded!',
                        'retryAfter' => $retryAfter
                    ]
                ],429);

            $method = $request->input('method', 'unknown');
            $methodCacheKey = "api_partner_{$partner['id']}_key_{$currentMinute}";

            // Set manual expiration time for cache
            if (!Cache::has($methodCacheKey)) {
                Cache::put($methodCacheKey, 0, now()->addMinutes(10));
            }

            $incrementedValue = Cache::increment($methodCacheKey);
            $methodCacheValue = "api_partner_{$partner['id']}_value_{$currentMinute}_{$incrementedValue}";
            Cache::put($methodCacheValue, $method, now()->addMinutes(10));


            Cache::put($cacheKey, $reqCount + 1, $retryAfter);
            if ($partner['is_active'] == 0)
                return response()->json([
                    'status' => false,
                    'error' => [
                        'message' => 'Partner is not active!'
                    ]
                ],401);


            try {
                $request->merge(['partner' => $partner]);
                $response = $next($request);
                try {
                    LogWriter::requests($request, $response, round((microtime(true) - $request_start) * 1000));
                }
                catch (\Exception $exception) {
                    Log::error(sprintf(
                        "Time: [%s]\nFile: %s\nLine: %d\nMessage: %s\nCode: %d\n%s\n",
                        now()->format('H:i:s'),
                        $exception->getFile(),
                        $exception->getLine(),
                        $exception->getMessage(),
                        $exception->getCode(),
                        str_repeat('-', 120)
                    ));
                }
                return $response;
            }
            catch (\Exception $exception)
            {
                return response()->json([
                    'status' => false,
                    'error' => [
                        "message" => [
                            'ru' => $exception->getMessage(),
                            'uz' => $exception->getMessage(),
                            'en' => $exception->getMessage(),
                        ],
                        "line" => $exception->getLine(),
                        "file" => $exception->getFile()
                    ]
                ],500);
            }
        }

        return response()->json(\App\Services\Helpers\Response::authFailed(),401);
    }
}

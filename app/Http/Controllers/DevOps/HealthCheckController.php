<?php

namespace App\Http\Controllers\DevOps;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthCheckController extends Controller
{
    public function check()
    {
        $schedule = Cache::get('app_scheduler_running');
        $status = [
            'app' => ['status' => 'OK', 'message' => 'Application is running.'],
            'database' => $this->safeCheck([$this, 'checkDatabaseConnection']),
            'cache' => $this->safeCheck([$this, 'checkCacheConnection']),
            'redis' => $this->safeCheck([$this, 'checkRedisConnection']),
            'scheduler' => [
                'status' => $schedule ? 'OK' : 'Error',
                'message' => $schedule ? "Scheduler is running. Last ran at {$schedule}" : 'Scheduler is not running.',
            ],

        ];

        $isHealthy = collect($status)->every(fn($service) => $service['status'] === 'OK');

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'details' => $status,
            'client_ip' => request()->ip(),
        ], $isHealthy ? 200 : 500);
    }

    private function safeCheck(callable $checkFunction)
    {
        try {
            return [
                'status' => 'OK',
                'message' => $checkFunction(),
            ];
        } catch (Throwable $e) {
            return [
                'status' => 'Error',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkDatabaseConnection()
    {
        try {
            // Check the database connection
            DB::connection()->getPdo();

            // Retrieve the database username from the environment
            $dbUser = config('database.connections.' . config('database.default') . '.username');
            $dbHost = config('database.connections.' . config('database.default') . '.host');

            return [
                'status' => 'OK',
                'message' => 'Database connection is healthy.',
                'user' => $dbUser,
                'host' => $dbHost
            ];
        } catch (Throwable $e) {
            // Retrieve the database username for debugging
            $dbUser = config('database.connections.' . config('database.default') . '.username');

            return [
                'status' => 'Error',
                'message' => 'Database connection error: ' . $e->getMessage(),
                'user' => $dbUser,
            ];
        }
    }


    private function checkCacheConnection()
    {
        try {
            Cache::store()->put('health_check', true, 1);
            return 'Cache connection is healthy.';
        } catch (Throwable $e) {
            throw new \Exception('Cache connection error: ' . $e->getMessage());
        }
    }

    private function checkRedisConnection()
    {
        try {
            Redis::ping();
            return 'Redis connection is healthy.';
        } catch (Throwable $e) {
            // Handle RedisException or other issues
            throw new \Exception('Redis connection error: ' . $e->getMessage());
        }
    }
}

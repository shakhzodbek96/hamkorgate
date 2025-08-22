<?php

namespace App\Services\Helpers;

use Illuminate\Support\Facades\Cache;

class Counter
{
    /**
     * Increment a counter by a given amount
     *
     * @param string $prefix
     * @param int $increment
     * @return void
     */
    protected static function updateCounter(string $prefix, int $increment = 1): void
    {
        $key = "{$prefix}_" . date('H_i');
        $current = Cache::get($key, 0);
        Cache::put($key, $current + $increment, 300);
    }

    /**
     * Get the count of a counter from the previous minute
     *
     * @param string $prefix
     * @return int
     */
    protected static function getCounter(string $prefix): int
    {
        $key = "{$prefix}_" . date('H_i', strtotime('-1 minute'));
        return (int) Cache::get($key, 0);
    }

    // Increment push count
    public static function svPushIncrement(int $increment=1): void
    {
        self::updateCounter('sv_push_count', $increment);
    }

    // Get push count from previous minute
    public static function svPushCount(): int
    {
        return self::getCounter('sv_push_count');
    }

    // Increment pushPay count
    public static function svPushPayIncrement(): void
    {
        self::updateCounter('sv_pushPay_count');
    }

    // Get pushPay count from previous minute
    public static function svPushPayCount(): int
    {
        return self::getCounter('sv_pushPay_count');
    }

    // Increment checker count by a given amount
    public static function svCheckerIncrement(int $count = 1): void
    {
        self::updateCounter('sv_checker_count', $count);
    }

    // Get checker count from previous minute
    public static function svCheckerCount(): int
    {
        return self::getCounter('sv_checker_count');
    }

    public static function svPayIncrement()
    {
        self::updateCounter('sv_pay_count');
    }

    public static function svPayCount()
    {
        return self::getCounter('sv_pay_count');
    }

    public static function HumoCheckerIncrement(int $count = 1): void
    {
        self::updateCounter('humo_checker_count', $count);
    }

    // Get checker count from previous minute
    public static function HumoCheckerCount(): int
    {
        return self::getCounter('humo_checker_count');
    }

    public static function HumoRateLimitIncrement(int $count = 1): void
    {
        self::updateCounter('humo_rateLimit_count', $count);
    }

    // Get checker count from previous minute
    public static function HumoRateLimitCount(): int
    {
        return self::getCounter('humo_rateLimit_count');
    }

    public static function HumoSuccessIncrement(int $count = 1): void
    {
        self::updateCounter('humo_success_count', $count);
    }

    // Get checker count from previous minute
    public static function HumoSuccessCount(): int
    {
        return self::getCounter('humo_success_count');
    }

    public static function HumoErrorCount(): int
    {
        return self::getCounter('humo_error_count');
    }

    public static function HumoErrorIncrement(int $count = 1): void
    {
        self::updateCounter('humo_error_count', $count);
    }

    public static function HumoPayIncrement()
    {
        self::updateCounter('humo_pay_count');
    }

    public static function HumoPayCount()
    {
        return self::getCounter('humo_pay_count');
    }

    public static function humoBalanceErrorIncrement(int $count = 1): void
    {
        self::updateCounter('humo_balance_error_count', $count);
    }

    // Get checker count from previous minute
    public static function humoBalanceErrorCount(): int
    {
        return self::getCounter('humo_balance_error_count');
    }
}

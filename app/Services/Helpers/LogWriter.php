<?php

namespace App\Services\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogWriter
{
    /**
     * Log an informational message.
     *
     * @param string $message
     * @return void
     */
    public static function info(string $message): void
    {
        Log::info($message);
    }

    /**
     * Log an exception with details.
     *
     * @param \Exception $exception
     * @param string|null $folder
     * @return void
     */
    public static function exception(\Exception $exception, ?string $folder = null): void
    {
        $message =  sprintf(
            "File: %s | Line: %d | Message: %s | Code: %d\n",
            $exception->getFile(),
            $exception->getLine(),
            $exception->getMessage(),
            $exception->getCode()
        );

        Log::error($message);
    }

    /**
     * Log an API request and response.
     *
     * @param Request $request
     * @param mixed $response
     * @param float|null $executionTime
     * @return void
     */
    public static function requests(Request $request, $response, ?float $executionTime = null): void
    {
        $headers = json_encode([
            'Authorization' => $request->header('Authorization'),
            'User-Agent' => $request->header('User-Agent'),
            'IP' => $request->header('x-original-forwarded-for'),
        ]);
        $body = $request->all();
        if (isset($body['partner']['config'])){
            unset($body['partner']['config']);
        }


        $message = sprintf(
            "Headers: %s | Body: %s | Response: %s | Execution Time: %s ms",
            $headers,
            json_encode($body),
            json_encode($response),
            $executionTime ?? 'N/A',
        );

        Log::info($message);
    }
}

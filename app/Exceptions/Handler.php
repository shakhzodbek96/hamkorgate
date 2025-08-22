<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Queue\MaxAttemptsExceededException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;
use Sentry\Laravel\Integration;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
    protected $dontReport = [
        MaxAttemptsExceededException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TooManyRequestsHttpException) {
            return response()->json([
                'status' => false,
                'error' => [
                    'message' => 'Too many requests. Please slow down.',
                    'retry_after' => $exception->getHeaders()['Retry-After'] ?? null, // Optionally include the retry time
                ]
            ], 429);
        }
        return parent::render($request, $exception);
    }
}

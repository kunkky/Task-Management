<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle method not allowed (wrong HTTP method)
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'HTTP method not allowed for this route. Please check and try again.',
            ], 405); // 405 Method Not Allowed
        }

        // Handle unauthorized access (token expired, etc.)
        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your token has expired or is invalid. Please log in again.',
            ], 401); // 401 Unauthorized
        }

        // Handle unauthenticated users
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authenticated. Please log in.',
            ], 401); // 401 Unauthorized
        }

        return parent::render($request, $exception);
    }
}

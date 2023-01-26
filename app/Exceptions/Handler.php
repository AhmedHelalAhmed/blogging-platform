<?php

namespace App\Exceptions;

use App\enums\DefaultMessageEnum;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;
use TypeError;

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
            // TODO change local to production when deploy
            if (app()->environment('local') && app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }

    public function render($request, Throwable $e)
    {
        // Add some custom Exception handling
        if ($e instanceof TypeError) {
            Log::error('[Exception] '.$e->getMessage(),
                [
                    'trace' => $e->getTrace(),
                    'Exception' => $e,
                ]);

            return redirect('/')->with('error', DefaultMessageEnum::ERROR_MESSAGE);
        }

        return parent::render($request, $e);
    }
}

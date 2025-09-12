<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Admin\ActionLog;
use Filament\Facades\Filament;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (Filament::isServing() && $this->shouldReport($e)) {
                ActionLog::updateCurrentActionLogToFailed($e);
            }
        });
    }
}

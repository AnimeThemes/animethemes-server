<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class LogRequest.
 */
class LogRequest
{
    /**
     * The list of parameters to exclude from logging.
     *
     * @var array
     */
    protected array $hidden = [
        'password',
        'current_password',
        'password_confirmation',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        '_token',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Log::info('Request Info', [
            'method' => $request->method(),
            'full-url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'parameters' => $request->except($this->hidden),
            'headers' => $request->headers->all(),
        ]);

        return $next($request);
    }
}

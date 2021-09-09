<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Auth\Invitation;
use Closure;
use Illuminate\Http\Request;

/**
 * Class HasOpenInvitation.
 */
class HasOpenInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $invitation = $request->route('invitation');

        if (! $invitation instanceof Invitation || ! $invitation->isOpen()) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Invitation;
use Closure;
use Illuminate\Http\Request;

/**
 * Class HasInvitation.
 */
class HasInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->get('token');

        $invitation = Invitation::where('token', $token)->first();

        if ($invitation === null || ! $invitation->isOpen()) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}

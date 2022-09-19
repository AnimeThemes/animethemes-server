<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use App\Models\Auth\Invitation;
use Closure;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class HasOpenInvitation.
 */
class HasOpenInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @return mixed
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws RuntimeException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $invitation = $request->route('invitation');

        if (! $invitation instanceof Invitation) {
            throw new RuntimeException('has_open_invitation should only be configured for invitations');
        }

        if (! $invitation->isOpen()) {
            abort(403, 'Closed Invitation');
        }

        return $next($request);
    }
}

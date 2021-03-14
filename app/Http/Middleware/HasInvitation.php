<?php

namespace App\Http\Middleware;

use App\Models\Invitation;
use Closure;

class HasInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->get('token');

        $invitation = Invitation::where('token', $token)->first();

        if ($invitation === null || ! $invitation->isOpen()) {
            return redirect(route('welcome.index'));
        }

        return $next($request);
    }
}

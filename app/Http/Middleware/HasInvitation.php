<?php

namespace App\Http\Middleware;

use App\Models\Invitation;
use Closure;
use Illuminate\Http\Request;

class HasInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->get('token');

        $invitation = Invitation::where('token', $token)->first();

        if ($invitation === null || ! $invitation->isOpen()) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}

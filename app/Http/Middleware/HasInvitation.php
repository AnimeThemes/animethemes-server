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
        // Token is required
        if (! $request->has('token')) {
            return redirect(route('welcome'));
        }

        $token = $request->get('token');

        try {
            $invitation = Invitation::where('token', $token)->firstOrFail();
            if (! $invitation->isOpen()) {
                return redirect(route('welcome'));
            }
        } catch (\Exception $exception) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}

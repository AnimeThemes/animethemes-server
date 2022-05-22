<?php

declare(strict_types=1);

namespace App\Contracts\Pipes;

use App\Models\Auth\User;
use Closure;

/**
 * Interface Pipe.
 */
interface Pipe
{
    /**
     * Handle an incoming request.
     *
     * @param  User  $user
     * @param  Closure(User): mixed  $next
     * @return mixed
     */
    public function handle(User $user, Closure $next): mixed;
}

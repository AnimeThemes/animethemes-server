<?php

declare(strict_types=1);

namespace App\GraphQL\Middleware;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Middleware;

class AuthMutation extends Middleware
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function handle($root, array $args, $context, ResolveInfo $resolveInfo, Closure $next)
    {
        throw_unless(Auth::check(), AuthorizationError::class, 'Unauthenticated.');

        return $next($root, $args, $context, $resolveInfo);
    }
}

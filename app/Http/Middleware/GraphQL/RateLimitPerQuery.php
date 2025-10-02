<?php

declare(strict_types=1);

namespace App\Http\Middleware\GraphQL;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use GraphQL\Language\Parser;
use GraphQL\Language\AST\OperationDefinitionNode;
use Illuminate\Http\Request;
use JsonException;

class RateLimitPerQuery
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $query = $request->input('query');
        $ip = $request->ip();
        $forwardedIp = $request->header('x-forwarded-ip');

        // (If request is from client and no forwarded ip) or (the user logged in has permission to bypass API rate limiting)
        /** @phpstan-ignore-next-line */
        if (($ip === '127.0.0.1' && ! $forwardedIp) || ($user?->can(SpecialPermission::BYPASS_GRAPHQL_RATE_LIMITER->value))) {
            return $next($request);
        }

        // Check if request is from client to prevent users from using forwarded ip
        if ($ip === '127.0.0.1' && $forwardedIp) {
            $ip = $forwardedIp;
        }

        if ($query) {
            try {
                $ast = Parser::parse($query);
            } catch (JsonException $e) {
                return $next($request);
            }

            $rootFields = 0;

            foreach ($ast->definitions as $definition) {
                if ($definition instanceof OperationDefinitionNode) {
                    $rootFields += count($definition->selectionSet->selections ?? []);
                }
            }

            $hits = max(1, $rootFields);

            $key = sprintf("graphql:%s", Auth::id() ?? $ip);

            foreach (range(1, $hits) as $_) {
                if (RateLimiter::tooManyAttempts($key, 80)) {
                    return response()->json([
                        'message' => 'Too many requests',
                    ], 429);
                }

                RateLimiter::hit($key);
            }
        }

        return $next($request)
            ->withHeaders([
                'X-RateLimit-Limit' => 80,
                'X-RateLimit-Remaining' => RateLimiter::remaining($key, 80),
            ]);
    }
}

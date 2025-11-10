<?php

declare(strict_types=1);

namespace App\Http\Middleware\GraphQL;

use App\Enums\Auth\SpecialPermission;
use Closure;
use GraphQL\Error\SyntaxError;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\Parser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use JsonException;

class RateLimitPerQuery
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $limit = Config::get('graphql.rate_limit');

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
            $rootFields = 0;

            try {
                $ast = Parser::parse($query);

                foreach ($ast->definitions as $definition) {
                    if ($definition instanceof OperationDefinitionNode) {
                        $rootFields += count($definition->selectionSet->selections ?? []);
                    }
                }
            } catch (JsonException|SyntaxError) {
                // Do nothing
            }

            $hits = max(1, $rootFields);

            $key = sprintf('graphql:%s', Auth::id() ?? $ip);

            foreach (range(1, $hits) as $_) {
                if (RateLimiter::tooManyAttempts($key, $limit)) {
                    $retryAfter = RateLimiter::availableIn($key);

                    return new JsonResponse([
                        'message' => 'Too Many Attempts.',
                    ], 429, [
                        'Retry-After' => $retryAfter,
                        'X-RateLimit-Limit' => $limit,
                        'X-RateLimit-Remaining' => RateLimiter::remaining($key, $limit),
                        'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->getTimestampMs(),
                    ]);
                }

                RateLimiter::hit($key);
            }

            return $next($request)
                ->withHeaders([
                    'X-RateLimit-Limit' => $limit,
                    'X-RateLimit-Remaining' => RateLimiter::remaining($key, $limit),
                ]);
        }

        return $next($request);
    }
}

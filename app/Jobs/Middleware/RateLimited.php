<?php

declare(strict_types=1);

namespace App\Jobs\Middleware;

use App\Concerns\DetectsRedis;
use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\Facades\Redis;

class RateLimited
{
    use DetectsRedis;

    /**
     * @throws LimiterTimeoutException
     */
    public function handle(mixed $job, callable $next): void
    {
        if ($this->appUsesRedis()) {
            Redis::throttle('key')
                ->block(0)
                ->allow(1)
                ->every(3)
                ->then(
                    function () use ($job, $next): void {
                        // Lock obtained...
                        $next($job);
                    },
                    function () use ($job): void {
                        // Could not obtain lock...
                        $job->release(5);
                    }
                );
        } else {
            $next($job);
        }
    }
}

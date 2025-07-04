<?php

declare(strict_types=1);

namespace App\Jobs\Middleware;

use App\Concerns\DetectsRedis;
use App\Enums\Models\List\ExternalProfileSite;
use App\Jobs\List\SyncExternalProfileJob;
use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

/**
 * Class ExternalProfileSiteRateLimited.
 */
class ExternalProfileSiteRateLimited
{
    use DetectsRedis;

    /**
     * Process the queued job.
     *
     * @param  SyncExternalProfileJob  $job
     * @param  callable  $next
     * @return void
     *
     * @throws LimiterTimeoutException
     */
    public function handle(mixed $job, callable $next): void
    {
        $definition = Arr::get($this->definition(), $job->profile->site->value);

        if ($this->appUsesRedis()) {
            Redis::throttle(Arr::get($definition, 'key'))
                ->block(0)
                ->allow(Arr::get($definition, 'allow'))
                ->every(Arr::get($definition, 'every'))
                ->then(
                    function () use ($job, $next) {
                        // Lock obtained...
                        $next($job);
                    },
                    function () use ($job) {
                        // Could not obtain lock...
                        $job->release(5);
                    }
                );
        } else {
            $next($job);
        }
    }

    /**
     * Get the rate limit definition of the site.
     *
     * @return array
     */
    protected function definition(): array
    {
        // Note: Full use is not allowed because of admin.
        return [
            ExternalProfileSite::ANILIST->value => [ // AniList rate limiting is 30/min
                'key' => ExternalProfileSite::ANILIST->name,
                'allow' => 1,
                'every' => 3,
            ],
            ExternalProfileSite::MAL->value => [ // MAL rate limiting is 90/min
                'key' => ExternalProfileSite::MAL->name,
                'allow' => 1,
                'every' => 1,
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs\List;

use App\Actions\Models\List\ExternalProfile\SyncExternalProfileAction;
use App\Features\AllowExternalProfileManagement;
use App\Jobs\Middleware\RateLimited;
use App\Models\List\ExternalProfile;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Pennant\Feature;

/**
 * Class SyncExternalProfileJob.
 */
class SyncExternalProfileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  ExternalProfile  $profile
     * @return void
     */
    public function __construct(protected readonly ExternalProfile $profile)
    {
        $this->onQueue('sync-external-profile');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (Feature::for(null)->active(AllowExternalProfileManagement::class)) {
            $action = new SyncExternalProfileAction();

            $action->handle($this->profile);
        }
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return [new RateLimited()];
    }

    /**
     * Determine the time at which the job should time out.
     *
     * @return DateTime
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(15);
    }
}

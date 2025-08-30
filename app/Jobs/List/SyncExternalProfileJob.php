<?php

declare(strict_types=1);

namespace App\Jobs\List;

use App\Actions\Models\List\External\SyncExternalProfileAction;
use App\Features\AllowExternalProfileManagement;
use App\Jobs\Middleware\ExternalProfileSiteRateLimited;
use App\Models\List\ExternalProfile;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Pennant\Feature;

class SyncExternalProfileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public readonly ExternalProfile $profile)
    {
        $this->onQueue("sync-external-profile-{$profile->site->name}");
    }

    public function handle(): void
    {
        if (Feature::for(null)->active(AllowExternalProfileManagement::class)) {
            $action = new SyncExternalProfileAction();

            $action->handle($this->profile);
        }
    }

    /**
     * @return array
     */
    public function middleware(): array
    {
        return [new ExternalProfileSiteRateLimited()];
    }

    /**
     * Determine the time at which the job should time out.
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(15);
    }
}

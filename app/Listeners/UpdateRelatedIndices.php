<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class UpdateRelatedIndices.
 */
class UpdateRelatedIndices implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  UpdateRelatedIndicesEvent  $event
     * @return void
     */
    public function handle(UpdateRelatedIndicesEvent $event)
    {
        $event->updateRelatedIndices();
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateRelatedIndices implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UpdateRelatedIndicesEvent $event): void
    {
        $event->updateRelatedIndices();
    }
}

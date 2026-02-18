<?php

declare(strict_types=1);

namespace App\Listeners\Wiki;

use App\Contracts\Events\UpdateAnimeSynonymsEvent;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UpdateAnimeSynonyms implements ShouldHandleEventsAfterCommit
{
    public function handle(UpdateAnimeSynonymsEvent $event): void
    {
        $event->updateAnimeSynonyms();
    }
}

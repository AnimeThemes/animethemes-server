<?php

declare(strict_types=1);

namespace App\Observers\Discord;

use App\Actions\Discord\DiscordThreadAction;
use App\Models\Discord\DiscordThread;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class DiscordThreadObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the DiscordThread "updated" event.
     */
    public function updated(DiscordThread $thread): void
    {
        DiscordThreadAction::getHttp()
            ->put('/thread', $thread->toArray())
            ->throw();
    }

    /**
     * Handle the DiscordThread "deleted" event.
     */
    public function deleted(DiscordThread $thread): void
    {
        DiscordThreadAction::getHttp()
            ->delete('/thread', ['id' => $thread->getKey()])
            ->throw();
    }
}

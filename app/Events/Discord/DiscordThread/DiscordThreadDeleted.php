<?php

declare(strict_types=1);

namespace App\Events\Discord\DiscordThread;

use App\Actions\Discord\DiscordThreadAction;
use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Discord\DiscordThread;

/**
 * @extends AdminDeletedEvent<DiscordThread>
 */
class DiscordThreadDeleted extends AdminDeletedEvent
{
    public function __construct(DiscordThread $thread)
    {
        parent::__construct($thread);
        $this->deleteThread();
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Discord Thread '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Delete the thread on discord.
     */
    protected function deleteThread(): void
    {
        DiscordThreadAction::getHttp()
            ->delete('/thread', ['id' => $this->getModel()->getKey()])
            ->throw();
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Discord\DiscordThread;

use App\Actions\Discord\DiscordThreadAction;
use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Discord\DiscordThread;

/**
 * @extends AdminUpdatedEvent<DiscordThread>
 */
class DiscordThreadUpdated extends AdminUpdatedEvent
{
    public function __construct(DiscordThread $thread)
    {
        parent::__construct($thread);
        $this->updateThread();
        $this->initializeEmbedFields($thread);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Discord Thread '**{$this->getModel()->getName()}**' has been updated.";
    }

    /**
     * Update the thread on discord.
     */
    protected function updateThread(): void
    {
        DiscordThreadAction::getHttp()
            ->put('/thread', $this->getModel()->toArray())
            ->throw();
    }
}

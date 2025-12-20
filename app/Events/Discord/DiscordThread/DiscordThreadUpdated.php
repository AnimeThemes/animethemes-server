<?php

declare(strict_types=1);

namespace App\Events\Discord\DiscordThread;

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
        $this->initializeEmbedFields($thread);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Discord Thread '**{$this->getModel()->getName()}**' has been updated.";
    }
}

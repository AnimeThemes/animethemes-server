<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Studio;

/**
 * @extends WikiUpdatedEvent<Studio>
 */
class StudioUpdated extends WikiUpdatedEvent
{
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
        $this->initializeEmbedFields($studio);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Studio '**{$this->getModel()->getName()}**' has been updated.";
    }
}

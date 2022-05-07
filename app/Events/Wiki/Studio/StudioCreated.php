<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Studio;

/**
 * Class StudioCreated.
 *
 * @extends WikiCreatedEvent<Studio>
 */
class StudioCreated extends WikiCreatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Studio  $studio
     */
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Studio
     */
    public function getModel(): Studio
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Studio '**{$this->getModel()->getName()}**' has been created.";
    }
}

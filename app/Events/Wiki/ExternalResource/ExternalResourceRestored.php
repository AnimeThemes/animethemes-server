<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceRestored.
 *
 * @extends WikiRestoredEvent<ExternalResource>
 */
class ExternalResourceRestored extends WikiRestoredEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ExternalResource  $resource
     */
    public function __construct(ExternalResource $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return ExternalResource
     */
    public function getModel(): ExternalResource
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
        return "Resource '**{$this->getModel()->getName()}**' has been restored.";
    }
}

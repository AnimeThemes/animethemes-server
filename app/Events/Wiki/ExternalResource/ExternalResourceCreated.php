<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\ExternalResource;

/**
 * @extends WikiCreatedEvent<ExternalResource>
 */
class ExternalResourceCreated extends WikiCreatedEvent
{
    public function __construct(ExternalResource $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): ExternalResource
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Resource '**{$this->getModel()->getName()}**' has been created.";
    }
}

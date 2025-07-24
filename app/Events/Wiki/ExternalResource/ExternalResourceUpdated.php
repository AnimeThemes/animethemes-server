<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceUpdated.
 *
 * @extends WikiUpdatedEvent<ExternalResource>
 */
class ExternalResourceUpdated extends WikiUpdatedEvent
{
    public function __construct(ExternalResource $resource)
    {
        parent::__construct($resource);
        $this->initializeEmbedFields($resource);
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
        return "Resource '**{$this->getModel()->getName()}**' has been updated.";
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\ExternalResource;

/**
 * @extends WikiUpdatedEvent<ExternalResource>
 */
class ExternalResourceUpdated extends WikiUpdatedEvent
{
    public function __construct(ExternalResource $resource)
    {
        parent::__construct($resource);
        $this->initializeEmbedFields($resource);
    }

    public function getModel(): ExternalResource
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Resource '**{$this->getModel()->getName()}**' has been updated.";
    }
}

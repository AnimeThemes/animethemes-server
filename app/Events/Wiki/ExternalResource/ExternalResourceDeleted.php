<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ExternalResource as ExternalResourceFilament;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceDeleted.
 *
 * @extends WikiDeletedEvent<ExternalResource>
 */
class ExternalResourceDeleted extends WikiDeletedEvent
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
        return "Resource '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Resource '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     *
     * @return string
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = ExternalResourceFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}

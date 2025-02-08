<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Admin\Feature;

/**
 * Class FeatureDeleted.
 *
 * @extends AdminDeletedEvent<Feature>
 */
class FeatureDeleted extends AdminDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Feature  $feature
     */
    public function __construct(Feature $feature)
    {
        parent::__construct($feature);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Feature
     */
    public function getModel(): Feature
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
        return "Feature '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     */
    public function shouldSendDiscordMessage(): bool
    {
        return $this->getModel()->isNullScope();
    }
}

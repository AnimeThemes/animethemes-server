<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Events\Base\Admin\AdminRestoredEvent;
use App\Models\List\ExternalProfile;

/**
 * Class ExternalProfileRestored.
 *
 * @extends AdminRestoredEvent<ExternalProfile>
 */
class ExternalProfileRestored extends AdminRestoredEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ExternalProfile  $profile
     */
    public function __construct(ExternalProfile $profile)
    {
        parent::__construct($profile);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return ExternalProfile
     */
    public function getModel(): ExternalProfile
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
        return "External Profile '**{$this->getModel()->getName()}**' has been restored.";
    }
}

<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\ExternalProfile;

/**
 * Class ExternalProfileDeleted.
 *
 * @extends ListDeletedEvent<ExternalProfile>
 */
class ExternalProfileDeleted extends ListDeletedEvent
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
     * Determine if the message should be sent.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return false;
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
        return "External Profile '**{$this->getModel()->getName()}**' has been deleted.";
    }
}

<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\ExternalProfile;

/**
 * @extends ListDeletedEvent<ExternalProfile>
 */
class ExternalProfileDeleted extends ListDeletedEvent
{
    public function __construct(ExternalProfile $profile)
    {
        parent::__construct($profile);
    }

    /**
     * Determine if the message should be sent.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): ExternalProfile
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "External Profile '**{$this->getModel()->getName()}**' has been deleted.";
    }
}

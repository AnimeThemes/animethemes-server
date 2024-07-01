<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile\ExternalEntry;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\ExternalProfile;
use App\Models\List\External\ExternalEntry;

/**
 * Class ExternalEntryDeleted.
 *
 * @extends ListDeletedEvent<ExternalEntry>
 */
class ExternalEntryDeleted extends ListDeletedEvent
{
    /**
     * The profile the entry belongs to.
     *
     * @var ExternalProfile
     */
    protected ExternalProfile $profile;

    /**
     * Create a new event instance.
     *
     * @param  ExternalEntry  $entry
     */
    public function __construct(ExternalEntry $entry)
    {
        parent::__construct($entry);
        $this->profile = $entry->externalprofile;
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
     * @return ExternalEntry
     */
    public function getModel(): ExternalEntry
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
        return "Entry '**{$this->getModel()->getName()}**' has been deleted for External Profile '**{$this->profile->getName()}**'.";
    }
}

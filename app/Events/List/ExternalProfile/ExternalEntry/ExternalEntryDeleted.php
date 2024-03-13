<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile\ExternalEntry;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\List\ExternalProfile;
use App\Models\List\External\ExternalEntry;

/**
 * Class ExternalEntryDeleted.
 *
 * @extends AdminDeletedEvent<ExternalEntry>
 */
class ExternalEntryDeleted extends AdminDeletedEvent
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

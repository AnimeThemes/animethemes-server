<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Song\Membership as MembershipFilament;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Video;

/**
 * Class MembershipDeleted.
 *
 * @extends WikiDeletedEvent<Membership>
 */
class MembershipDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Membership  $membership
     */
    public function __construct(Membership $membership)
    {
        parent::__construct($membership);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Membership
     */
    public function getModel(): Membership
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
        return "Membership '**{$this->getModel()->member->getName()}**' of Group '**{$this->getModel()->artist->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Membership '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     *
     * @return string
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = MembershipFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_ARTIST, Membership::RELATION_MEMBER]);

        $membership->artist->searchable();
        $membership->member->searchable();
    }
}

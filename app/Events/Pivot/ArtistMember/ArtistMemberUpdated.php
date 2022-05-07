<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistMember;

use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\Artist;
use App\Pivots\ArtistMember;

/**
 * Class ArtistMemberUpdated.
 *
 * @extends PivotUpdatedEvent<Artist, Artist>
 */
class ArtistMemberUpdated extends PivotUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ArtistMember  $artistMember
     */
    public function __construct(ArtistMember $artistMember)
    {
        parent::__construct($artistMember->artist, $artistMember->member);
        $this->initializeEmbedFields($artistMember);
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Member '**{$foreign->getName()}**' for Artist '**{$related->getName()}**' has been updated.";
    }
}

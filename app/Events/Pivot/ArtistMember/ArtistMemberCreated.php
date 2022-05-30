<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistMember;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Artist;
use App\Pivots\ArtistMember;

/**
 * Class ArtistMemberCreated.
 *
 * @extends PivotCreatedEvent<Artist, Artist>
 */
class ArtistMemberCreated extends PivotCreatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ArtistMember  $artistMember
     */
    public function __construct(ArtistMember $artistMember)
    {
        parent::__construct($artistMember->artist, $artistMember->member);
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

        return "Member '**{$foreign->getName()}**' has been attached to Artist '**{$related->getName()}**'.";
    }
}

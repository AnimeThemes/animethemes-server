<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistMember;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;

/**
 * Class ArtistMemberDeleted.
 *
 * @extends PivotDeletedEvent<Artist, Artist>
 */
class ArtistMemberDeleted extends PivotDeletedEvent
{
    public function __construct(ArtistMember $artistMember)
    {
        parent::__construct($artistMember->artist, $artistMember->member);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Member '**{$foreign->getName()}**' has been detached from Artist '**{$related->getName()}**'.";
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistMember;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;

/**
 * @extends PivotCreatedEvent<Artist, Artist>
 */
class ArtistMemberCreated extends PivotCreatedEvent
{
    public function __construct(ArtistMember $artistMember)
    {
        parent::__construct($artistMember->artist, $artistMember->member);
    }

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Member '**{$foreign->getName()}**' has been attached to Artist '**{$related->getName()}**'.";
    }
}

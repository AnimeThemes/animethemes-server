<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistMember;

use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;

/**
 * @extends PivotUpdatedEvent<Artist, Artist>
 */
class ArtistMemberUpdated extends PivotUpdatedEvent
{
    public function __construct(ArtistMember $artistMember)
    {
        parent::__construct($artistMember->artist, $artistMember->member);
        $this->initializeEmbedFields($artistMember);
    }

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Member '**{$foreign->getName()}**' for Artist '**{$related->getName()}**' has been updated.";
    }
}

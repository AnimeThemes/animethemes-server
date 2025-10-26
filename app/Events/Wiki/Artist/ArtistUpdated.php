<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Artist;

/**
 * @extends WikiUpdatedEvent<Artist>
 */
class ArtistUpdated extends WikiUpdatedEvent
{
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
        $this->initializeEmbedFields($artist);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Artist '**{$this->getModel()->getName()}**' has been updated.";
    }
}

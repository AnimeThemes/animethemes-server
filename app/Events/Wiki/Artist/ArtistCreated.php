<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Artist;

/**
 * @extends WikiCreatedEvent<Artist>
 */
class ArtistCreated extends WikiCreatedEvent
{
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
    }

    public function getModel(): Artist
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Artist '**{$this->getModel()->getName()}**' has been created.";
    }
}

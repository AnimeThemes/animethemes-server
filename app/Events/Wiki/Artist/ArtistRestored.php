<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Artist;

/**
 * @extends WikiRestoredEvent<Artist>
 */
class ArtistRestored extends WikiRestoredEvent
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
        return "Artist '**{$this->getModel()->getName()}**' has been restored.";
    }
}

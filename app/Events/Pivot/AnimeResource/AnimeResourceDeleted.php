<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeResource;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;

/**
 * Class AnimeResourceDeleted.
 *
 * @extends PivotDeletedEvent<Anime, ExternalResource>
 */
class AnimeResourceDeleted extends PivotDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeResource  $animeResource
     */
    public function __construct(AnimeResource $animeResource)
    {
        parent::__construct($animeResource->anime, $animeResource->resource);
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

        return "Resource '**{$foreign->getName()}**' has been detached from Anime '**{$related->getName()}**'.";
    }
}

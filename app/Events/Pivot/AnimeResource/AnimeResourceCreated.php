<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeResource;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;

/**
 * Class AnimeResourceCreated.
 *
 * @extends PivotCreatedEvent<Anime, ExternalResource>
 */
class AnimeResourceCreated extends PivotCreatedEvent
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

        return "Resource '**{$foreign->getName()}**' has been attached to Anime '**{$related->getName()}**'.";
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeResource;

use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;

/**
 * Class AnimeResourceUpdated.
 *
 * @extends PivotUpdatedEvent<Anime, ExternalResource>
 */
class AnimeResourceUpdated extends PivotUpdatedEvent
{
    public function __construct(AnimeResource $animeResource)
    {
        parent::__construct($animeResource->anime, $animeResource->resource);
        $this->initializeEmbedFields($animeResource);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Resource '**{$foreign->getName()}**' for Anime '**{$related->getName()}**' has been updated.";
    }
}

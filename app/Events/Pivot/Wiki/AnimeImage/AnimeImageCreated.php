<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeImage;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;

/**
 * Class AnimeImageCreated.
 *
 * @extends PivotCreatedEvent<Anime, Image>
 */
class AnimeImageCreated extends PivotCreatedEvent
{
    public function __construct(AnimeImage $animeImage)
    {
        parent::__construct($animeImage->anime, $animeImage->image);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Image '**{$foreign->getName()}**' has been attached to Anime '**{$related->getName()}**'.";
    }
}

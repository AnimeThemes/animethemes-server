<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeImage;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;

/**
 * Class AnimeImageDeleted.
 *
 * @extends PivotDeletedEvent<Anime, Image>
 */
class AnimeImageDeleted extends PivotDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeImage  $animeImage
     */
    public function __construct(AnimeImage $animeImage)
    {
        parent::__construct($animeImage->anime, $animeImage->image);
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

        return "Image '**{$foreign->getName()}**' has been detached from Anime '**{$related->getName()}**'.";
    }
}

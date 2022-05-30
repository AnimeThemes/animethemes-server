<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeStudio;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\AnimeStudio;

/**
 * Class AnimeStudioDeleted.
 *
 * @extends PivotDeletedEvent<Studio, Anime>
 */
class AnimeStudioDeleted extends PivotDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeStudio  $animeStudio
     */
    public function __construct(AnimeStudio $animeStudio)
    {
        parent::__construct($animeStudio->studio, $animeStudio->anime);
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

        return "Anime '**{$foreign->getName()}**' has been detached from Studio '**{$related->getName()}**'.";
    }
}

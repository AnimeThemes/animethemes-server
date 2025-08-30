<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeStudio;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;

/**
 * @extends PivotDeletedEvent<Studio, Anime>
 */
class AnimeStudioDeleted extends PivotDeletedEvent
{
    public function __construct(AnimeStudio $animeStudio)
    {
        parent::__construct($animeStudio->studio, $animeStudio->anime);
    }

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Anime '**{$foreign->getName()}**' has been detached from Studio '**{$related->getName()}**'.";
    }
}

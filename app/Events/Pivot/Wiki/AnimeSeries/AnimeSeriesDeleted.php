<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeSeries;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;

/**
 * @extends PivotDeletedEvent<Series, Anime>
 */
class AnimeSeriesDeleted extends PivotDeletedEvent
{
    public function __construct(AnimeSeries $animeSeries)
    {
        parent::__construct($animeSeries->series, $animeSeries->anime);
    }

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Anime '**{$foreign->getName()}**' has been detached from Series '**{$related->getName()}**'.";
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeSeries;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;

/**
 * Class AnimeSeriesCreated.
 *
 * @extends PivotCreatedEvent<Series, Anime>
 */
class AnimeSeriesCreated extends PivotCreatedEvent
{
    public function __construct(AnimeSeries $animeSeries)
    {
        parent::__construct($animeSeries->series, $animeSeries->anime);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Anime '**{$foreign->getName()}**' has been attached to Series '**{$related->getName()}**'.";
    }
}

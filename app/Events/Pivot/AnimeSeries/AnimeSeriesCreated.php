<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeSeries;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\AnimeSeries;

/**
 * Class AnimeSeriesCreated.
 *
 * @extends PivotCreatedEvent<Series, Anime>
 */
class AnimeSeriesCreated extends PivotCreatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeSeries  $animeSeries
     */
    public function __construct(AnimeSeries $animeSeries)
    {
        parent::__construct($animeSeries->series, $animeSeries->anime);
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

        return "Anime '**{$foreign->getName()}**' has been attached to Series '**{$related->getName()}**'.";
    }
}

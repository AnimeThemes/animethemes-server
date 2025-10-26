<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Series;

/**
 * @extends WikiUpdatedEvent<Series>
 */
class SeriesUpdated extends WikiUpdatedEvent
{
    public function __construct(Series $series)
    {
        parent::__construct($series);
        $this->initializeEmbedFields($series);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Series '**{$this->getModel()->getName()}**' has been updated.";
    }
}

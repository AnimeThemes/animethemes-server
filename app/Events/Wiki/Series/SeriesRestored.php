<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Series;

/**
 * @extends WikiRestoredEvent<Series>
 */
class SeriesRestored extends WikiRestoredEvent
{
    public function __construct(Series $series)
    {
        parent::__construct($series);
    }

    public function getModel(): Series
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Series '**{$this->getModel()->getName()}**' has been restored.";
    }
}

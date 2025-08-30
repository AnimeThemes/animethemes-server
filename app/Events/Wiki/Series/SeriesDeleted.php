<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Series as SeriesFilament;
use App\Models\Wiki\Series;

/**
 * @extends WikiDeletedEvent<Series>
 */
class SeriesDeleted extends WikiDeletedEvent
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
        return "Series '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Series '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return SeriesFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

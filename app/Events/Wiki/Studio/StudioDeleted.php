<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Studio as StudioFilament;
use App\Models\Wiki\Studio;

/**
 * @extends WikiDeletedEvent<Studio>
 */
class StudioDeleted extends WikiDeletedEvent
{
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
    }

    public function getModel(): Studio
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Studio '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Studio '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return StudioFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

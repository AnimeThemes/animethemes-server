<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Anime as AnimeFilament;
use App\Models\Wiki\Anime;

/**
 * @extends WikiDeletedEvent<Anime>
 */
class AnimeDeleted extends WikiDeletedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Anime '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Anime '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return AnimeFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

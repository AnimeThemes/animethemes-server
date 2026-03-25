<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\AnimeResource as AnimeFilament;
use App\Models\Wiki\Anime;

/**
 * @extends WikiDeletedEvent<Anime>
 */
class AnimeDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return AnimeFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ArtistResource as ArtistFilament;
use App\Models\Wiki\Artist;

/**
 * @extends WikiDeletedEvent<Artist>
 */
class ArtistDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return ArtistFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

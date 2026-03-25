<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ImageResource as ImageFilament;
use App\Models\Wiki\Image;

/**
 * @extends WikiDeletedEvent<Image>
 */
class ImageDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return ImageFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

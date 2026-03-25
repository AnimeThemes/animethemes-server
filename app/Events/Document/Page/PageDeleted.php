<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Document\DocumentDeletedEvent;
use App\Filament\Resources\Document\PageResource as PageFilament;
use App\Models\Document\Page;

/**
 * @extends DocumentDeletedEvent<Page>
 */
class PageDeleted extends DocumentDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return PageFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

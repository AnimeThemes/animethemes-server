<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Document\Page as PageFilament;
use App\Models\Document\Page;

/**
 * @extends WikiDeletedEvent<Page>
 */
class PageDeleted extends WikiDeletedEvent
{
    public function __construct(Page $page)
    {
        parent::__construct($page);
    }

    public function getModel(): Page
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Page '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return PageFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ExternalResource as ExternalResourceFilament;
use App\Models\Wiki\ExternalResource;

/**
 * @extends WikiDeletedEvent<ExternalResource>
 */
class ExternalResourceDeleted extends WikiDeletedEvent
{
    public function __construct(ExternalResource $resource)
    {
        parent::__construct($resource);
    }

    public function getModel(): ExternalResource
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Resource '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Resource '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return ExternalResourceFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

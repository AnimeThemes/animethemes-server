<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Studio;

/**
 * @extends WikiCreatedEvent<Studio>
 */
class StudioCreated extends WikiCreatedEvent
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
        return "Studio '**{$this->getModel()->getName()}**' has been created.";
    }
}

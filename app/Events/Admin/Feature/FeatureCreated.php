<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Events\Base\Admin\AdminCreatedEvent;
use App\Models\Admin\Feature;

/**
 * @extends AdminCreatedEvent<Feature>
 */
class FeatureCreated extends AdminCreatedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Feature '**{$this->getModel()->getName()}**' has been created.";
    }

    public function shouldSendDiscordMessage(): bool
    {
        return $this->getModel()->isNullScope();
    }
}

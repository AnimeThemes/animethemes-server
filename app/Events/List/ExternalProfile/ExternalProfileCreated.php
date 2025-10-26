<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Events\Base\List\ListCreatedEvent;
use App\Models\List\ExternalProfile;

/**
 * @extends ListCreatedEvent<ExternalProfile>
 */
class ExternalProfileCreated extends ListCreatedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "External Profile '**{$this->getModel()->getName()}**' has been created.";
    }
}

<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\ExternalProfile;

/**
 * @extends ListDeletedEvent<ExternalProfile>
 */
class ExternalProfileDeleted extends ListDeletedEvent
{
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "External Profile '**{$this->getModel()->getName()}**' has been deleted.";
    }
}

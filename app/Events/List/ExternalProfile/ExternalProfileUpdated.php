<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile;

use App\Events\Base\List\ListUpdatedEvent;
use App\Models\List\ExternalProfile;

/**
 * @extends ListUpdatedEvent<ExternalProfile>
 */
class ExternalProfileUpdated extends ListUpdatedEvent
{
    public function __construct(ExternalProfile $profile)
    {
        parent::__construct($profile);
        $this->initializeEmbedFields($profile);
    }

    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "External Profile '**{$this->getModel()->getName()}**' has been updated.";
    }
}

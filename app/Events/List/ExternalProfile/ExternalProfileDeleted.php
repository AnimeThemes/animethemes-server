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
    public function __construct(ExternalProfile $profile)
    {
        parent::__construct($profile);
    }

    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    public function getModel(): ExternalProfile
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "External Profile '**{$this->getModel()->getName()}**' has been deleted.";
    }
}

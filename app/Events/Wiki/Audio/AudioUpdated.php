<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Audio;

/**
 * @extends WikiUpdatedEvent<Audio>
 */
class AudioUpdated extends WikiUpdatedEvent
{
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
        $this->initializeEmbedFields($audio);
    }

    public function getModel(): Audio
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been updated.";
    }
}

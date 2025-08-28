<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Audio as AudioFilament;
use App\Models\Wiki\Audio;

/**
 * Class AudioDeleted.
 *
 * @extends WikiDeletedEvent<Audio>
 */
class AudioDeleted extends WikiDeletedEvent
{
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Audio
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Audio '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        return AudioFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

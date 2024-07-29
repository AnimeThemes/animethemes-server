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
    /**
     * Create a new event instance.
     *
     * @param  Audio  $audio
     */
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Audio
     */
    public function getModel(): Audio
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Audio '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     *
     * @return string
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = AudioFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}

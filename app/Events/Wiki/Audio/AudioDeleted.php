<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Models\Wiki\Audio;
use App\Nova\Resources\Wiki\Audio as AudioResource;

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
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Audio '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNovaNotificationUrl(): string
    {
        $uriKey = AudioResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}

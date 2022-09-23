<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Models\Wiki\Video\VideoScript;
use App\Nova\Resources\Wiki\Video\Script;

/**
 * Class VideoScriptDeleted.
 *
 * @extends WikiDeletedEvent<VideoScript>
 */
class VideoScriptDeleted extends WikiDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  VideoScript  $script
     */
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return VideoScript
     */
    public function getModel(): VideoScript
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
        return "Script '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNotificationUrl(): string
    {
        $uriKey = Script::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}

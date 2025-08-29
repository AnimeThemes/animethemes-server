<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Video\Script as VideoScriptFilament;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class VideoScriptDeleted.
 *
 * @extends WikiDeletedEvent<VideoScript>
 */
class VideoScriptDeleted extends WikiDeletedEvent
{
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): VideoScript
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        return VideoScriptFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

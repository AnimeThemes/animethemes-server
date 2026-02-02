<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Video\ScriptResource as VideoScriptFilament;
use App\Models\Wiki\Video\VideoScript;

/**
 * @extends WikiDeletedEvent<VideoScript>
 */
class VideoScriptDeleted extends WikiDeletedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return VideoScriptFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

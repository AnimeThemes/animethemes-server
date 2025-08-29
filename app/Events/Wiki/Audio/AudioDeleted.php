<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Audio as AudioFilament;
use App\Models\Wiki\Audio;

/**
 * @extends WikiDeletedEvent<Audio>
 */
class AudioDeleted extends WikiDeletedEvent
{
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
    }

    public function getModel(): Audio
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Audio '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return AudioFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

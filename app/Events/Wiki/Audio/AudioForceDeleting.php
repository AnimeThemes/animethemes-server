<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Audio;

/**
 * @extends BaseEvent<Audio>
 */
class AudioForceDeleting extends BaseEvent implements RemoveFromStorageEvent
{
    /**
     * Remove the audio from the bucket.
     */
    public function removeFromStorage(): void
    {
        $action = new DeleteAudioAction($this->getModel());

        $action->handle();
    }
}

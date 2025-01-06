<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Audio;

/**
 * Class AudioForceDeleting.
 *
 * @extends BaseEvent<Audio>
 */
class AudioForceDeleting extends BaseEvent implements RemoveFromStorageEvent
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
     * Remove the image from the bucket.
     *
     * @return void
     */
    public function removeFromStorage(): void
    {
        $action = new DeleteAudioAction($this->getModel());

        $action->handle();
    }
}

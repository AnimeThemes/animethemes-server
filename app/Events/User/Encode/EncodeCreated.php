<?php

declare(strict_types=1);

namespace App\Events\User\Encode;

use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Contracts\Events\ManagesTrackEvent;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\BaseEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\User\Encode;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class EncodeCreated.
 *
 * @extends BaseEvent<Encode>
 */
class EncodeCreated extends BaseEvent implements ManagesTrackEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(Encode $encode)
    {
        parent::__construct($encode);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Encode
    {
        return $this->model;
    }

    /**
     * Manages a track in a playlist.
     */
    public function manageTrack(): void
    {
        $video = $this->getModel()->video;

        if ($video->animethemeentries->isEmpty()) {
            return;
        }

        $playlist = Playlist::query()->firstOrCreate([
            Playlist::ATTRIBUTE_NAME => 'Encodes',
            Playlist::ATTRIBUTE_USER => $this->getModel()->user_id,
        ], [
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
            Playlist::ATTRIBUTE_DESCRIPTION => 'Auto-generated playlist for encodes.',
        ]);

        $action = new StoreTrackAction();

        $action->store($playlist, PlaylistTrack::query(), [
            PlaylistTrack::ATTRIBUTE_ENTRY => $video->animethemeentries->first()->getKey(),
            PlaylistTrack::ATTRIBUTE_VIDEO => $video->getKey(),
        ]);
    }
}

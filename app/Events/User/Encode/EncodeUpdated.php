<?php

declare(strict_types=1);

namespace App\Events\User\Encode;

use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Contracts\Events\ManagesTrackEvent;
use App\Enums\Models\User\EncodeType;
use App\Events\BaseEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\User\Encode;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class EncodeUpdated.
 *
 * @extends BaseEvent<Encode>
 */
class EncodeUpdated extends BaseEvent implements ManagesTrackEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Encode  $encode
     */
    public function __construct(Encode $encode)
    {
        parent::__construct($encode);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Encode
     */
    public function getModel(): Encode
    {
        return $this->model;
    }

    /**
     * Manages a track in a playlist.
     *
     * @return void
     */
    public function manageTrack(): void
    {
        if (
            $this->getModel()->wasChanged(Encode::ATTRIBUTE_TYPE) &&
            $this->getModel()->getAttribute(Encode::ATTRIBUTE_TYPE) === EncodeType::OLD->value
        ) {
            $track = PlaylistTrack::query()
                ->with([PlaylistTrack::RELATION_PLAYLIST, PlaylistTrack::RELATION_VIDEO])
                ->whereRelation(PlaylistTrack::RELATION_PLAYLIST, Playlist::ATTRIBUTE_NAME, 'Encodes')
                ->whereBelongsTo($this->getModel()->video, PlaylistTrack::RELATION_VIDEO)
                ->first();

            if ($track instanceof PlaylistTrack) {
                $removeAction = new RemoveTrackAction();

                $removeAction->remove($track->playlist, $track);
            }
        }
    }
}

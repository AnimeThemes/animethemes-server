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
 * @extends BaseEvent<Encode>
 */
class EncodeUpdated extends BaseEvent implements ManagesTrackEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(Encode $encode)
    {
        parent::__construct($encode);
    }

    public function getModel(): Encode
    {
        return $this->model;
    }

    public function manageTrack(): void
    {
        if (
            $this->getModel()->wasChanged(Encode::ATTRIBUTE_TYPE) &&
            $this->getModel()->getAttribute(Encode::ATTRIBUTE_TYPE) === EncodeType::OLD
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

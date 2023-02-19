<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist;

use App\Actions\Http\Api\StoreAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StoreTrackAction.
 */
class StoreTrackAction
{
    /**
     * Store playlist track.
     *
     * @param  Playlist  $playlist
     * @param  Builder  $builder
     * @param  array  $parameters
     * @return Model
     */
    public function store(Playlist $playlist, Builder $builder, array $parameters): Model
    {
        $trackParameters = array_merge(
            $parameters,
            [PlaylistTrack::ATTRIBUTE_PLAYLIST => $playlist->getKey()]
        );

        $storeAction = new StoreAction();

        $track = $storeAction->store($builder, $trackParameters);

        if ($playlist->first()->doesntExist()) {
            $playlist->first()->associate($track)->save();
        }

        return $track;
    }
}

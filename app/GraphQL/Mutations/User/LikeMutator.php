<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Models\List\Playlist;
use App\Models\Wiki\Video;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Class LikeMutator.
 */
class LikeMutator
{
    final public const ATTRIBUTE_PLAYLIST = 'playlist';
    final public const ATTRIBUTE_VIDEO = 'video';

    /**
     * Store a newly created resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return Model
     *
     * @throws Exception
     */
    public function store($_, array $args): Model
    {
        $playlist = Arr::get($args, self::ATTRIBUTE_PLAYLIST);
        $video = Arr::get($args, self::ATTRIBUTE_VIDEO);

        if ($playlist instanceof Playlist) {
            $playlist->like();
            return $playlist;
        }

        if ($video instanceof Video) {
            $video->like();
            return $video;
        }

        throw new Exception('None models detected to like.');
    }

    /**
     * Remove the specified resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return Model
     *
     * @throws Exception
     */
    public function destroy($_, array $args): Model
    {
        $playlist = Arr::get($args, self::ATTRIBUTE_PLAYLIST);
        $video = Arr::get($args, self::ATTRIBUTE_VIDEO);

        if ($playlist instanceof Playlist) {
            $playlist->unlike();
            return $playlist;
        }

        if ($video instanceof Video) {
            $video->unlike();
            return $video;
        }

        throw new Exception('None models detected to unlike.');
    }
}

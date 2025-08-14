<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\User;

use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Mutations\Models\User\LikeMutation;
use App\GraphQL\Definition\Mutations\Models\User\UnlikeMutation;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Class LikeController.
 *
 * @extends BaseController<Like>
 */
class LikeController extends BaseController
{
    final public const ATTRIBUTE_PLAYLIST = 'playlist';
    final public const ATTRIBUTE_VIDEO = 'video';

    /**
     * Store a newly created resource.
     *
     * @param  null  $root
     * @param  array<string, mixed>  $args
     *
     * @throws ClientValidationException
     */
    public function store($root, array $args): Model
    {
        $validated = $this->validated($args, LikeMutation::class);

        $playlist = Arr::get($validated, self::ATTRIBUTE_PLAYLIST);
        $video = Arr::get($validated, self::ATTRIBUTE_VIDEO);

        if ($playlist instanceof Playlist) {
            $playlist->like();

            return $playlist;
        }

        if ($video instanceof Video) {
            $video->like();

            return $video;
        }

        throw new ClientValidationException('One resource is required to like.');
    }

    /**
     * Remove the specified resource.
     *
     * @param  null  $root
     * @param  array<string, mixed>  $args
     *
     * @throws ClientValidationException
     */
    public function destroy($root, array $args): Model
    {
        $validated = $this->validated($args, UnlikeMutation::class);

        $playlist = Arr::get($validated, self::ATTRIBUTE_PLAYLIST);
        $video = Arr::get($validated, self::ATTRIBUTE_VIDEO);

        if ($playlist instanceof Playlist) {
            $playlist->unlike();

            return $playlist;
        }

        if ($video instanceof Video) {
            $video->unlike();

            return $video;
        }

        throw new ClientValidationException('One resource is required to unlike.');
    }
}

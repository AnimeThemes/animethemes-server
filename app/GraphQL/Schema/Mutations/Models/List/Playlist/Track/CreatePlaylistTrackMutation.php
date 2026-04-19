<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist\Track;

use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Schema\Mutations\Models\CreateMutation;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\Http\Middleware\Models\List\PlaylistExceedsTrackLimit;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class CreatePlaylistTrackMutation extends CreateMutation
{
    public static Playlist $playlist;

    final public const string ATTRIBUTE_ENTRY = 'entryId';
    final public const string ATTRIBUTE_VIDEO = 'videoId';

    public function __construct()
    {
        parent::__construct(PlaylistTrack::class);
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistTrackType
    {
        return new PlaylistTrackType();
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, StoreTrackAction $action): PlaylistTrack
    {
        static::$playlist = Arr::get($args, 'playlist');

        $this->runHttpMiddlewares([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            PlaylistExceedsTrackLimit::class,
        ]);

        $validated = Validator::make($args, $this->rulesForValidation($args))->validated();

        $validated = [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($validated, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($validated, 'videoId'),
        ];

        return $action->store(static::$playlist, PlaylistTrack::query(), $validated);
    }
}

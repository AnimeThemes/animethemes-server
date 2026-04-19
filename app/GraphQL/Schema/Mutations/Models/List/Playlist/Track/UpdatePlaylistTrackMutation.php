<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist\Track;

use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Schema\Mutations\Models\UpdateMutation;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class UpdatePlaylistTrackMutation extends UpdateMutation
{
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
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, UpdateTrackAction $action): PlaylistTrack
    {
        $this->runHttpMiddlewares([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        /** @var PlaylistTrack $track */
        $track = Arr::get($args, 'model');

        $validated = Validator::make($args, $this->rulesForValidation($args))->validated();

        $validated = [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($validated, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($validated, 'videoId'),
        ];

        return $action->update($track->playlist, $track, $validated);
    }
}

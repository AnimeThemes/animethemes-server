<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist\Track;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Schema\Mutations\Models\DeleteMutation;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Schema\Types\MessageResponseType;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeletePlaylistTrackMutation extends DeleteMutation
{
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

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(new MessageResponseType()->name()));
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, string>
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, DestroyTrackAction $action): array
    {
        $this->runHttpMiddlewares([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        /** @var PlaylistTrack $track */
        $track = Arr::pull($args, 'model');

        $message = $action->destroy($track->playlist, $track);

        return [
            'message' => $message,
        ];
    }
}

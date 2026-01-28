<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist\Track;

use App\GraphQL\Resolvers\List\Playlist\PlaylistTrackResolver;
use App\GraphQL\Schema\Mutations\Models\CreateMutation;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\App;

class CreatePlaylistTrackMutation extends CreateMutation
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class);
    }

    public function description(): string
    {
        return 'Create playlist track';
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
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(PlaylistTrackResolver::class)
            ->store($root, $args);
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist\Track;

use App\GraphQL\Controllers\List\Playlist\PlaylistTrackController;
use App\GraphQL\Schema\Mutations\Models\DeleteMutation;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Schema\Types\MessageResponseType;
use App\Models\List\Playlist\PlaylistTrack;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeletePlaylistTrackMutation extends DeleteMutation
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class);
    }

    public function description(): string
    {
        return 'Delete playlist track';
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
        return Type::nonNull(GraphQL::type(new MessageResponseType()->getName()));
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(PlaylistTrackController::class)
            ->destroy($root, $args);
    }
}

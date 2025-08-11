<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models\List\Playlist;

use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Mutations\Models\CreateMutation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\App;

class CreatePlaylistMutation extends CreateMutation
{
    public function __construct()
    {
        parent::__construct(Playlist::class);
    }

    /**
     * The description of the mutation.
     */
    public function description(): string
    {
        return 'Create playlist';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PlaylistType
    {
        return new PlaylistType();
    }

    /**
     * Resolve the mutation.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(PlaylistController::class)
            ->store($root, $args, $context, $resolveInfo);
    }
}

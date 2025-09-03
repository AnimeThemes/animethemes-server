<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist;

use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Schema\Mutations\Models\UpdateMutation;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\App;

class UpdatePlaylistMutation extends UpdateMutation
{
    public function __construct()
    {
        parent::__construct(Playlist::class);
    }

    public function description(): string
    {
        return 'Update playlist';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PlaylistType
    {
        return new PlaylistType();
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(PlaylistController::class)
            ->update($root, $args);
    }
}

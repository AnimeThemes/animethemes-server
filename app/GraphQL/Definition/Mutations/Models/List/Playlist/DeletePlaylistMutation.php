<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Models\List\Playlist;

use App\GraphQL\Controllers\List\PlaylistController;
use App\GraphQL\Definition\Mutations\Models\DeleteMutation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\MessageResponseType;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeletePlaylistMutation extends DeleteMutation
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
        return 'Delete playlist';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PlaylistType
    {
        return new PlaylistType();
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(new MessageResponseType()->getName()));
    }

    /**
     * Resolve the mutation.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(PlaylistController::class)
            ->destroy($root, $args, $context, $resolveInfo);
    }
}

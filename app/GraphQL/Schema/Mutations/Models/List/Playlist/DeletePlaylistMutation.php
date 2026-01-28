<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist;

use App\GraphQL\Resolvers\List\PlaylistResolver;
use App\GraphQL\Schema\Mutations\Models\DeleteMutation;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\MessageResponseType;
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

    public function description(): string
    {
        return 'Delete playlist';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistType
    {
        return new PlaylistType();
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
        return App::make(PlaylistResolver::class)
            ->destroy($root, $args);
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist;

use App\Actions\Http\Api\DestroyAction;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Schema\Mutations\Models\DeleteMutation;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\MessageResponseType;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeletePlaylistMutation extends DeleteMutation
{
    public function __construct()
    {
        parent::__construct(Playlist::class);
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
        return Type::nonNull(GraphQL::type(new MessageResponseType()->name()));
    }

    /**
     * @param  array<string, mixed>  $args
     * @param  DestroyAction<Playlist>  $action
     * @return array<string, string>
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, DestroyAction $action): array
    {
        $this->runHttpMiddlewares([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, 'model');

        $message = $action->forceDelete($playlist);

        return [
            'message' => $message,
        ];
    }
}

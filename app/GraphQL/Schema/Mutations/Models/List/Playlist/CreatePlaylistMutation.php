<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist;

use App\Actions\Http\Api\StoreAction;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Schema\Mutations\Models\CreateMutation;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\Http\Middleware\Models\List\UserExceedsPlaylistLimit;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class CreatePlaylistMutation extends CreateMutation
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

    /**
     * @param  array<string, mixed>  $args
     * @param  StoreAction<Playlist>  $action
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, StoreAction $action): Playlist
    {
        $this->runHttpMiddlewares([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            UserExceedsPlaylistLimit::class,
        ]);

        $validated = Validator::make($args, $this->rulesForValidation($args))->validated();

        $parameters = [
            ...$validated,
            Playlist::ATTRIBUTE_USER => Auth::id(),
        ];

        return $action->store(Playlist::query(), $parameters);
    }
}

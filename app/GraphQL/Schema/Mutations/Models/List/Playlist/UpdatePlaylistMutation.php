<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\List\Playlist;

use App\Actions\Http\Api\UpdateAction;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Schema\Mutations\Models\UpdateMutation;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class UpdatePlaylistMutation extends UpdateMutation
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
     * @param  UpdateAction<Playlist>  $action
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, UpdateAction $action): Playlist
    {
        $this->runHttpMiddlewares([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, 'model');

        $validated = Validator::make($args, $this->rulesForValidation($args))->validated();

        return $action->update($playlist, $validated);
    }
}

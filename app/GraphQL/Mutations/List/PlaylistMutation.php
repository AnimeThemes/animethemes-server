<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\List;

use App\Actions\Http\Api\DestroyAction;
use App\Concerns\GraphQL\RunMiddlewares;
use App\Concerns\GraphQL\ValidateArgs;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Validators\List\CreatePlaylistMutationValidator;
use App\GraphQL\Validators\List\UpdatePlaylistMutationValidator;
use App\Http\Middleware\Models\List\UserExceedsPlaylistLimit;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PlaylistMutation
{
    use RunMiddlewares;
    use ValidateArgs;

    /**
     * @param  array<string, mixed>  $args
     */
    public function create(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Playlist
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            UserExceedsPlaylistLimit::class,
        ]);

        $validated = $this->validated(CreatePlaylistMutationValidator::class, $resolveInfo);

        $parameters = [
            ...$validated,
            Playlist::ATTRIBUTE_USER => Auth::id(),
        ];

        return Playlist::query()->create($parameters);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Playlist
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            UserExceedsPlaylistLimit::class,
        ]);

        $validated = $this->validated(UpdatePlaylistMutationValidator::class, $resolveInfo);

        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, Arr::pull($args, 'id'));

        $playlist->update($validated);

        return $playlist->refresh();
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function delete(null $_, array $args): array
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        $playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, Arr::pull($args, 'id'));

        $message = new DestroyAction()->forceDelete($playlist);

        return [
            'message' => $message,
        ];
    }
}

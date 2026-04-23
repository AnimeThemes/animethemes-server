<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Concerns\GraphQL\BindModels;
use App\Concerns\GraphQL\ValidateArgs;
use App\GraphQL\Validators\User\ToggleLikeMutationValidator;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ToggleLikeMutation
{
    use BindModels;
    use ValidateArgs;

    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?Like
    {
        $validated = $this->validated(ToggleLikeMutationValidator::class, $resolveInfo);

        if ($entryId = Arr::get($validated, 'entryId')) {
            $likeable = $this->bind(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID, $entryId);
        } elseif ($playlistId = Arr::get($validated, 'playlistId')) {
            $likeable = $this->bind(Playlist::class, Playlist::ATTRIBUTE_HASHID, $playlistId);
        } else {
            return null;
        }

        return $likeable?->toggleLike(Auth::user());
    }
}

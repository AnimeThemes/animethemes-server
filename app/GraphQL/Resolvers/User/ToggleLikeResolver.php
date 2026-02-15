<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers\User;

use App\Contracts\Models\Likeable;
use App\GraphQL\Resolvers\BaseResolver;
use App\GraphQL\Schema\Mutations\Models\User\ToggleLikeMutation;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @extends BaseResolver<Like>
 */
class ToggleLikeResolver extends BaseResolver
{
    final public const string ATTRIBUTE_ENTRY = 'entry';
    final public const string ATTRIBUTE_PLAYLIST = 'playlist';

    /**
     * @param  array<string, mixed>  $args
     */
    public function store($root, array $args): ?Like
    {
        $validated = $this->validated($args, ToggleLikeMutation::class);

        /** @var Model&Likeable $likeable */
        $likeable = Arr::first($validated);

        return $likeable->toggleLike(Auth::user());
    }
}

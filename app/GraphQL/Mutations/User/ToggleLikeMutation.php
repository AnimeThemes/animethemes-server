<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Concerns\GraphQL\ValidateArgs;
use App\Contracts\Models\Likeable;
use App\GraphQL\Validators\User\ToggleLikeMutationValidator;
use App\Models\User\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ToggleLikeMutation
{
    use ValidateArgs;

    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?Like
    {
        $validated = $this->validated(ToggleLikeMutationValidator::class, $resolveInfo);

        /** @var Model&Likeable $likeable */
        $likeable = Arr::first($validated);

        return $likeable->toggleLike(Auth::user());
    }
}

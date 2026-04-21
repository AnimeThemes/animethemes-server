<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Concerns\GraphQL\ValidateArgs;
use App\GraphQL\Validators\User\WatchMutationValidator;
use App\Models\User\WatchHistory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class WatchMutation
{
    use ValidateArgs;

    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?WatchHistory
    {
        $validated = $this->validated(WatchMutationValidator::class, $resolveInfo);

        $validated = [
            WatchHistory::ATTRIBUTE_ENTRY => Arr::integer($args, 'entryId'),
            WatchHistory::ATTRIBUTE_VIDEO => Arr::integer($args, 'videoId'),
            WatchHistory::ATTRIBUTE_USER => Auth::id(),
        ];

        return WatchHistory::query()->create($validated);
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers\User;

use App\Actions\Http\Api\StoreAction;
use App\GraphQL\Resolvers\BaseResolver;
use App\GraphQL\Schema\Mutations\Models\User\WatchMutation;
use App\Models\User\WatchHistory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class WatchResolver extends BaseResolver
{
    final public const string ATTRIBUTE_ENTRY = 'entryId';
    final public const string ATTRIBUTE_VIDEO = 'videoId';

    /**
     * @param  array<string, mixed>  $args
     * @param  StoreAction<WatchHistory>  $action
     */
    public function store(array $args, StoreAction $action): WatchHistory
    {
        $this->runMiddleware();

        $validated = $this->validated($args, WatchMutation::class);

        $validated += [
            WatchHistory::ATTRIBUTE_ENTRY => Arr::integer($validated, 'entryId'),
            WatchHistory::ATTRIBUTE_VIDEO => Arr::integer($validated, 'videoId'),
            WatchHistory::ATTRIBUTE_USER => Auth::id(),
        ];

        return $action->store(WatchHistory::query(), $validated);
    }
}

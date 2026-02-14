<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers\User;

use App\GraphQL\Resolvers\BaseResolver;
use App\GraphQL\Schema\Mutations\Models\User\WatchMutation;
use App\Models\User\WatchHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @extends BaseResolver<WatchHistory>
 */
class WatchResolver extends BaseResolver
{
    final public const string ATTRIBUTE_ENTRY = 'entryId';
    final public const string ATTRIBUTE_VIDEO = 'videoId';

    /**
     * @param  array<string, mixed>  $args
     * @return WatchHistory
     */
    public function store($root, array $args): Model
    {
        $validated = $this->validated($args, WatchMutation::class);

        $validated += [
            WatchHistory::ATTRIBUTE_ENTRY => Arr::integer($validated, 'entryId'),
            WatchHistory::ATTRIBUTE_VIDEO => Arr::integer($validated, 'videoId'),
            WatchHistory::ATTRIBUTE_USER => Auth::id(),
        ];

        return $this->storeAction->store(WatchHistory::query(), $validated);
    }
}

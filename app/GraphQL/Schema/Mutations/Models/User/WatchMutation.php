<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\User;

use App\Actions\Http\Api\StoreAction;
use App\GraphQL\Schema\Mutations\Models\CreateMutation;
use App\GraphQL\Schema\Types\User\WatchHistoryType;
use App\Models\User\WatchHistory;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class WatchMutation extends CreateMutation
{
    final public const string ATTRIBUTE_ENTRY = 'entryId';
    final public const string ATTRIBUTE_VIDEO = 'videoId';

    public function __construct()
    {
        parent::__construct(WatchHistory::class);
    }

    public function name(): string
    {
        return 'Watch';
    }

    public function description(): string
    {
        return 'Mark a video as watched.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return ($this->response = Gate::inspect('create', WatchHistory::class))->allowed();
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): WatchHistoryType
    {
        return new WatchHistoryType();
    }

    /**
     * @param  array<string, mixed>  $args
     * @param  StoreAction<WatchHistory>  $action
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, StoreAction $action): WatchHistory
    {
        $validated = Validator::make($args, $this->rulesForValidation($args))->validated();

        $validated = [
            WatchHistory::ATTRIBUTE_ENTRY => Arr::integer($validated, 'entryId'),
            WatchHistory::ATTRIBUTE_VIDEO => Arr::integer($validated, 'videoId'),
            WatchHistory::ATTRIBUTE_USER => Auth::id(),
        ];

        return $action->store(WatchHistory::query(), $validated);
    }
}

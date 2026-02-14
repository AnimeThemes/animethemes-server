<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Models\User;

use App\GraphQL\Resolvers\List\PlaylistResolver;
use App\GraphQL\Schema\Mutations\Models\CreateMutation;
use App\GraphQL\Schema\Types\User\WatchHistoryType;
use App\Models\User\WatchHistory;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class WatchMutation extends CreateMutation
{
    public function __construct()
    {
        parent::__construct(WatchHistory::class, 'Watch');
    }

    public function description(): string
    {
        return 'Mark a video as watched.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return ($this->response = Gate::inspect('create', [WatchHistory::class, ...$args]))->allowed();
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
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(PlaylistResolver::class)
            ->store($root, $args);
    }
}

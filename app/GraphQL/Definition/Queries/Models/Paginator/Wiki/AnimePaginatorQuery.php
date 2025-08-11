<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Policies\Wiki\AnimePolicy;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;

class AnimePaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('animePaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of anime resources given fields.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return new AnimePolicy()->viewAny(Auth::user(), $args);
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnimeType
    {
        return new AnimeType();
    }
}

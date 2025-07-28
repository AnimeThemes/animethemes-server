<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List;

use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Controllers\BaseController;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @extends BaseController<ExternalProfile>
 */
class ExternalProfileController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<ExternalProfile>  $builder
     * @param  array  $args
     * @return Builder<ExternalProfile>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        $builder->where(ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, ExternalProfile::RELATION_USER);
        }

        return $builder;
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List\External;

use App\GraphQL\Controllers\BaseController;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @extends BaseController<ExternalEntry>
 */
class ExternalEntryController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<ExternalEntry>  $builder
     * @param  array  $args
     * @return Builder<ExternalEntry>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($args, 'profile');

        $builder->where(ExternalEntry::ATTRIBUTE_PROFILE, $profile->getKey());

        return $builder;
    }
}

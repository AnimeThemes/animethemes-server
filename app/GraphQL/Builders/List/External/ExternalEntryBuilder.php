<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List\External;

use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class ExternalEntryBuilder.
 */
class ExternalEntryBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<ExternalEntry>  $builder
     * @param  mixed  $value
     * @param  mixed  $root
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return Builder<ExternalEntry>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($args, 'profile');

        $builder->where(ExternalEntry::ATTRIBUTE_PROFILE, $profile->getKey());

        return $builder;
    }
}

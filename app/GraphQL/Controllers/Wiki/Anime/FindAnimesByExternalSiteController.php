<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Wiki\Anime;

use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Queries\Wiki\FindAnimesByExternalSiteQuery;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @extends BaseController<Anime>
 */
class FindAnimesByExternalSiteController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the show query.
     *
     * @param  Builder<Anime>  $builder
     * @param  array  $args
     * @return Builder<Anime>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        $site = Arr::get($args, FindAnimesByExternalSiteQuery::ATTRIBUTE_SITE);
        $externalId = Arr::get($args, FindAnimesByExternalSiteQuery::ATTRIBUTE_ID);
        $link = Arr::get($args, FindAnimesByExternalSiteQuery::ATTRIBUTE_LINK);

        if (is_null($externalId) && is_null($link)) {
            throw new ClientValidationException('At least "id" or "link" is required.');
        }

        $builder->whereRelation(Anime::RELATION_RESOURCES, function (Builder $query) use ($site, $externalId, $link) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $site);

            if (is_int($externalId)) {
                $query->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $externalId);
            }

            if (is_string($link)) {
                $query->where(ExternalResource::ATTRIBUTE_LINK, $link);
            }
        });

        return $builder;
    }
}

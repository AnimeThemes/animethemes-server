<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\Wiki;

use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Definition\Queries\Wiki\Anime\FindAnimesByExternalSiteQuery;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FindAnimesByExternalSiteBuilder
{
    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Anime>  $builder
     * @param  array  $args
     * @return Builder<Anime>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        $site = Arr::get($args, FindAnimesByExternalSiteQuery::ATTRIBUTE_SITE);
        $externalId = Arr::get($args, FindAnimesByExternalSiteQuery::ATTRIBUTE_ID);
        $link = Arr::get($args, FindAnimesByExternalSiteQuery::ATTRIBUTE_LINK);

        $builder->whereRelation(Anime::RELATION_RESOURCES, ExternalResource::ATTRIBUTE_SITE, $site);

        if (is_null($externalId) && is_null($link)) {
            throw new ClientValidationException('At least "id" or "link" is required.');
        }

        if (is_int($externalId)) {
            $builder->whereRelation(Anime::RELATION_RESOURCES, ExternalResource::ATTRIBUTE_EXTERNAL_ID, $externalId);
        }

        if (is_string($link)) {
            $builder->whereRelation(Anime::RELATION_RESOURCES, ExternalResource::ATTRIBUTE_LINK, $link);
        }

        return $builder;
    }
}

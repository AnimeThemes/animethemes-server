<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\Wiki\Anime;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Queries\Wiki\FindAnimeByExternalSiteQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @extends BaseController<Anime>
 */
class FindAnimeByExternalSiteController extends BaseController
{
    use ConstrainsEagerLoads;

    /**
     * @param  array  $args
     * @return Builder<Anime>
     */
    public function index(mixed $root, array $args, $context, $resolveInfo): Builder
    {
        $builder = Anime::query();

        $site = Arr::get($args, FindAnimeByExternalSiteQuery::ATTRIBUTE_SITE);
        $externalId = Arr::get($args, FindAnimeByExternalSiteQuery::ATTRIBUTE_ID);
        $link = Arr::get($args, FindAnimeByExternalSiteQuery::ATTRIBUTE_LINK);

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

        $this->constrainEagerLoads($builder, $resolveInfo, new AnimeType());

        return $builder;
    }
}

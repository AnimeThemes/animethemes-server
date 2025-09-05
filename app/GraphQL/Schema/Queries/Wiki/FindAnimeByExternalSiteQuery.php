<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Support\Argument\Argument;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Rebing\GraphQL\Support\Facades\GraphQL;

class FindAnimeByExternalSiteQuery extends BaseQuery
{
    final public const ATTRIBUTE_SITE = 'site';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_LINK = 'link';

    public function __construct()
    {
        parent::__construct('findAnimeByExternalSite', false, true);
    }

    public function description(): string
    {
        return 'Filter anime by its external id on given site.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return Gate::allows('viewAny', Anime::class);
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            new Argument(self::ATTRIBUTE_SITE, GraphQL::type(class_basename(ResourceSite::class)))
                ->required(),

            new Argument(self::ATTRIBUTE_ID, Type::int()),

            new Argument(self::ATTRIBUTE_LINK, Type::string()),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeType
    {
        return new AnimeType();
    }

    /**
     * @return Collection
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        $builder = Anime::query();

        $site = Arr::get($args, self::ATTRIBUTE_SITE);
        $externalId = Arr::get($args, self::ATTRIBUTE_ID);
        $link = Arr::get($args, self::ATTRIBUTE_LINK);

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

        return $builder->get();
    }
}

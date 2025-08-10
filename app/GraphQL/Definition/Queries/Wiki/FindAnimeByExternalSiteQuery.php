<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\GraphQL\Controllers\Wiki\Anime\FindAnimeByExternalSiteController;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Policies\Wiki\AnimePolicy;
use App\GraphQL\Support\Argument\Argument;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Schema\TypeRegistry;

class FindAnimeByExternalSiteQuery extends BaseQuery
{
    final public const ATTRIBUTE_SITE = 'site';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_LINK = 'link';

    public function __construct()
    {
        parent::__construct('findAnimeByExternalSite', false, true);
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Filter anime by its external id on given site.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return new AnimePolicy()->viewAny(Auth::user(), $args);
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            new Argument(self::ATTRIBUTE_SITE, app(TypeRegistry::class)->get(class_basename(ResourceSite::class)))
                ->required(),

            new Argument(self::ATTRIBUTE_ID, Type::int()),

            new Argument(self::ATTRIBUTE_LINK, Type::string()),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnimeType
    {
        return new AnimeType();
    }

    /**
     * @return Collection
     */
    public function resolve($root, array $args, $context, $resolveInfo, $getSelectFields)
    {
        return App::make(FindAnimeByExternalSiteController::class)
            ->index(func_get_args());
    }
}

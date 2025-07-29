<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\GraphQL\Attributes\Resolvers\UseAllDirective;
use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Controllers\Wiki\Anime\FindAnimesByExternalSiteController;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Support\Argument;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

#[UseAllDirective]
#[UseBuilderDirective(FindAnimesByExternalSiteController::class, 'show')]
class FindAnimesByExternalSiteQuery extends BaseQuery
{
    final public const ATTRIBUTE_SITE = 'site';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_LINK = 'link';

    public function __construct()
    {
        parent::__construct('findAnimesByExternalSite', false, false);
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Filter animes by its external id on given site.';
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            ...parent::directives(),

            'canModel' => [
                'ability' => 'viewAny',
                'injectArgs' => 'true',
                'model' => Anime::class,
            ],
        ];
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
    public function baseType(): Type
    {
        return Type::listof(Type::nonNull(new AnimeType()));
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\GraphQL\Attributes\UseBuilderDirective;
use App\GraphQL\Builders\Wiki\FindAnimesByExternalSiteBuilder;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

#[UseBuilderDirective(FindAnimesByExternalSiteBuilder::class)]
class FindAnimesByExternalSiteQuery extends BaseQuery
{
    final public const ATTRIBUTE_SITE = 'site';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_LINK = 'link';

    public function __construct()
    {
        parent::__construct('findAnimesByExternalSite', false, false, false);
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

            'all' => [],

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
            new Argument(self::ATTRIBUTE_SITE, app(TypeRegistry::class)->get(class_basename(ResourceSite::class)), true),
            new Argument(self::ATTRIBUTE_ID, Type::int(), false),
            new Argument(self::ATTRIBUTE_LINK, Type::string(), false),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): Type
    {
        return Type::listof(new AnimeType());
    }
}

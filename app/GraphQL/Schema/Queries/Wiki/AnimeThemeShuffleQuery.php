<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Wiki;

use App\Enums\GraphQL\Filter\ComparisonOperator;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\ThemeType;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\FirstArgument;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AnimeThemeShuffleQuery extends BaseQuery
{
    final public const string ATTRIBUTE_TYPE = 'type';
    final public const string ATTRIBUTE_MEDIA_FORMAT = 'mediaFormat';
    final public const string ATTRIBUTE_YEAR_LTE = 'year_lte';
    final public const string ATTRIBUTE_YEAR_GTE = 'year_gte';
    final public const string ATTRIBUTE_SPOILER = 'spoiler';

    public function __construct()
    {
        parent::__construct('animethemeShuffle', false, true);
    }

    public function description(): string
    {
        return 'Shuffle themes.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return ($this->response = Gate::inspect('viewAny', AnimeTheme::class))->allowed();
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            new FirstArgument(),

            new Argument(self::ATTRIBUTE_TYPE, Type::listOf(Type::nonNull(GraphQL::type(class_basename(ThemeType::class))))),

            new Argument(self::ATTRIBUTE_MEDIA_FORMAT, Type::listOf(Type::nonNull(GraphQL::type(class_basename(AnimeMediaFormat::class))))),

            new Argument(self::ATTRIBUTE_YEAR_LTE, Type::int()),

            new Argument(self::ATTRIBUTE_YEAR_GTE, Type::int()),

            new Argument(self::ATTRIBUTE_SPOILER, Type::boolean())
                ->required()
                ->withDefaultValue(false),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeThemeType
    {
        return new AnimeThemeType();
    }

    public function resolve($root, array $args, $ctx, ResolveInfo $resolveInfo): Collection
    {
        $builder = AnimeTheme::query();

        if (is_array($types = Arr::get($args, self::ATTRIBUTE_TYPE))) {
            $builder->whereIn(AnimeTheme::ATTRIBUTE_TYPE, Arr::map($types, fn (ThemeType $type) => $type->value));
        }

        $builder->whereHas(AnimeTheme::RELATION_ANIME, function (Builder $query) use ($args): void {
            if (is_array($formats = Arr::get($args, self::ATTRIBUTE_MEDIA_FORMAT))) {
                $query->whereIn(Anime::ATTRIBUTE_MEDIA_FORMAT, Arr::map($formats, fn (AnimeMediaFormat $format) => $format->value));
            }

            if (is_int($yearLte = Arr::get($args, self::ATTRIBUTE_YEAR_LTE))) {
                $query->where(Anime::ATTRIBUTE_YEAR, ComparisonOperator::LTE->value, $yearLte);
            }

            if (is_int($yearGte = Arr::get($args, self::ATTRIBUTE_YEAR_GTE))) {
                $query->where(Anime::ATTRIBUTE_YEAR, ComparisonOperator::GTE->value, $yearGte);
            }
        });

        $builder->whereRelation(AnimeTheme::RELATION_ENTRIES, AnimeThemeEntry::ATTRIBUTE_SPOILER, Arr::boolean($args, self::ATTRIBUTE_SPOILER));

        $builder->inRandomOrder();

        $this->constrainEagerLoads($builder, $resolveInfo, new AnimeThemeType());

        return $builder->paginate(Arr::get($args, 'first'), page: 1)->getCollection();
    }
}

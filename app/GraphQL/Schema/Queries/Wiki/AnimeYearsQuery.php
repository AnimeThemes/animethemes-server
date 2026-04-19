<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Wiki;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonSeasonField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonsField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearYearField;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeYearType;
use App\Models\Wiki\Anime;
use App\Rules\GraphQL\Resolver\AnimeYearRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AnimeYearsQuery extends BaseQuery
{
    final public const string ARGUMENT_YEAR = 'year';

    public function __construct()
    {
        parent::__construct(false, true);
    }

    public function name(): string
    {
        return 'animeyears';
    }

    public function description(): string
    {
        return 'Returns a list of years grouped by its seasons.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $model = Arr::pull($args, 'model');

        $args = collect($args)
            ->filter(fn ($value): bool => $value instanceof Model)
            ->values()
            ->all();

        return ($this->response = Gate::inspect('viewAny', [Anime::class, $model, ...$args]))->allowed();
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            new Argument(self::ARGUMENT_YEAR, Type::listOf(Type::nonNull(Type::int()))),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnimeYearType
    {
        return new AnimeYearType();
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): Collection
    {
        $year = Arr::get($args, self::ARGUMENT_YEAR);

        $fieldSelection = $resolveInfo->getFieldSelection(1);

        Validator::make(['year' => $year], ['year' => new AnimeYearRule($fieldSelection)])
            ->validate();

        return Anime::query()
            ->distinct([Anime::ATTRIBUTE_YEAR, Anime::ATTRIBUTE_SEASON])
            ->orderBy(Anime::ATTRIBUTE_YEAR)
            ->when($year !== null, fn (Builder $query) => $query->whereIn(Anime::ATTRIBUTE_YEAR, $year))
            ->get([Anime::ATTRIBUTE_YEAR, Anime::ATTRIBUTE_SEASON])
            ->groupBy(Anime::ATTRIBUTE_YEAR)
            ->map(fn (Collection $items, int $year): array => [
                AnimeYearYearField::FIELD => $year,

                AnimeYearSeasonsField::FIELD => $items
                    ->map(fn (Anime $anime): array => [
                        AnimeYearSeasonSeasonField::FIELD => $anime->season,
                        'seasonLocalized' => $anime->season->localize(),
                        'year' => $year, // Needed to query animes on the 'seasons' field.
                    ])
                    ->unique(Anime::ATTRIBUTE_SEASON)
                    ->values()
                    ->toArray(),
            ])->values();
    }
}

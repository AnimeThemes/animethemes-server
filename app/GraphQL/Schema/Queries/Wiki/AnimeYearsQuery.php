<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Wiki;

use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Support\Argument\Argument;
use App\Models\Wiki\Anime;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class AnimeYearsQuery extends BaseQuery
{
    final public const ARGUMENT_YEAR = 'year';

    public function __construct()
    {
        parent::__construct('animeyears', false, true);
    }

    public function description(): string
    {
        return 'Returns a list of years grouped by its seasons.';
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $model = Arr::pull($args, 'model');

        $args = collect($args)
            ->filter(fn ($value) => $value instanceof Model)
            ->values()
            ->all();

        return Gate::allows('viewAny', [Anime::class, $model, ...$args]);
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

    /**
     * @return Collection
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        return App::make(AnimeYearsController::class)
            ->index($root, $args, $context, $resolveInfo);
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Policies\Wiki\AnimePolicy;
use App\GraphQL\Support\Argument\Argument;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AnimeYearsQuery extends BaseQuery
{
    final public const ARGUMENT_YEAR = 'year';

    public function __construct()
    {
        parent::__construct('animeyears', false, true);
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a list of years grouped by its seasons.';
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
            new Argument(self::ARGUMENT_YEAR, Type::listOf(Type::nonNull(Type::int()))),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnimeYearType
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

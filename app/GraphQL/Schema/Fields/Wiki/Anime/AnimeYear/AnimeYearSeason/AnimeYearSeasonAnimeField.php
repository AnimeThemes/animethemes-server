<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\HasArgumentsField;
use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\AnimePaginationQuery;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AnimeYearSeasonAnimeField extends Field implements DisplayableField, HasArgumentsField
{
    final public const FIELD = 'anime';

    public function __construct()
    {
        parent::__construct(self::FIELD, nullable: false);
    }

    public function description(): string
    {
        return 'The animes of the season year filtered';
    }

    public function baseType(): AnimeType
    {
        return new AnimeType();
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::paginate($this->baseType()->getName()));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }

    /**
     * Get the arguments of the field.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return new AnimePaginationQuery()->arguments();
    }

    /**
     * @return Paginator
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(AnimeYearsController::class)
            ->resolveAnimeField($root, $args, $context, $resolveInfo);
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\HasArgumentsField;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\AnimePaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;

#[UsePaginateDirective(true, AnimeYearsController::class.'@applyBuilderToAnimeField')]
class AnimeYearSeasonAnimeField extends Field implements DisplayableField, HasArgumentsField
{
    final public const FIELD = 'anime';

    public function __construct()
    {
        parent::__construct(self::FIELD, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The animes of the season year filtered';
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return new AnimeType();
    }

    /**
     * Determine if the field should be displayed to the user.
     */
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
        return new AnimePaginatorQuery()->arguments();
    }
}

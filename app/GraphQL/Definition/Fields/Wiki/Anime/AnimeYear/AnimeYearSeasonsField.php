<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonType;
use GraphQL\Type\Definition\Type;

class AnimeYearSeasonsField extends Field implements DisplayableField
{
    final public const FIELD = 'seasons';

    public function __construct()
    {
        parent::__construct(self::FIELD);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The list of season objects that composite the year';
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::listOf(Type::nonNull(new AnimeYearSeasonType()));
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}

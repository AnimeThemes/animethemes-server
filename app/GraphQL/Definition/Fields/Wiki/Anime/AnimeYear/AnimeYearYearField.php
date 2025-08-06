<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Type;

class AnimeYearYearField extends Field implements DisplayableField
{
    final public const FIELD = Anime::ATTRIBUTE_YEAR;

    public function __construct()
    {
        parent::__construct(self::FIELD, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The year of the AnimeYear type';
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::int();
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use GraphQL\Type\Definition\Type;

class AnimeYearSpringField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('spring');
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The spring season of the year';
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::listOf(Type::nonNull(new AnimeType()));
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use GraphQL\Type\Definition\Type;

/**
 * Class AnimeYearWinterField.
 */
class AnimeYearWinterField extends Field implements DisplayableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct('winter');
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The winter season of the year';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::listOf(new AnimeType());
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @return bool
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}

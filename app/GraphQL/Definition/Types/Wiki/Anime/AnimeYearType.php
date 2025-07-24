<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearFallField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSpringField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSummerField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearWinterField;
use App\GraphQL\Definition\Types\BaseType;

class AnimeYearType extends BaseType implements HasFields
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'The anime year response type, grouped by season.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new AnimeYearWinterField(),
            new AnimeYearSpringField(),
            new AnimeYearSummerField(),
            new AnimeYearFallField(),
        ];
    }
}

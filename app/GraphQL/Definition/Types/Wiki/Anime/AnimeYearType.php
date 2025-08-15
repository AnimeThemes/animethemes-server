<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonsField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearYearField;
use App\GraphQL\Definition\Types\BaseType;

class AnimeYearType extends BaseType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'The anime year response type, grouped by season.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new AnimeYearYearField(),
            new AnimeYearSeasonField(),
            new AnimeYearSeasonsField(),
        ];
    }
}

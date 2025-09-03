<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Anime;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeasonsField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearYearField;
use App\GraphQL\Schema\Types\BaseType;

class AnimeYearType extends BaseType
{
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

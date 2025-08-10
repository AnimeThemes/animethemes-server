<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime\AnimeYear;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonAnimeField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonSeasonField;
use App\GraphQL\Definition\Types\BaseType;

class AnimeYearSeasonsType extends BaseType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'The anime year season type.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new AnimeYearSeasonSeasonField(),
            new LocalizedEnumField(new AnimeYearSeasonSeasonField()),
            new AnimeYearSeasonAnimeField(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Anime\AnimeYear;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonAnimeField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason\AnimeYearSeasonSeasonField;
use App\GraphQL\Schema\Types\BaseType;

class AnimeYearSeasonsType extends BaseType
{
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

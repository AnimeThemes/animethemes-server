<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYear\AnimeYearSeason;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Enums\Models\Wiki\AnimeSeason;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\Wiki\Anime;

class AnimeYearSeasonSeasonField extends EnumField implements DisplayableField
{
    final public const FIELD = Anime::ATTRIBUTE_SEASON;

    public function __construct()
    {
        parent::__construct(self::FIELD, AnimeSeason::class, nullable: false);
    }

    public function description(): string
    {
        return 'The season of the anime year';
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}

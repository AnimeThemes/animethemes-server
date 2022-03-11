<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\Anime;

/**
 * Class AnimeSeasonField.
 */
class AnimeSeasonField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SEASON, AnimeSeason::class);
    }
}

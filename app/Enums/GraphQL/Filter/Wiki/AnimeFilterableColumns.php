<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Deprecated;

enum AnimeFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case NAME;
    #[Deprecated('Use FORMAT instead')]
    case MEDIA_FORMAT;
    case FORMAT;
    case SEASON;
    case SYNOPSIS;
    case YEAR;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Anime::ATTRIBUTE_ID),
            self::NAME => new StringFilter($this->name, Anime::ATTRIBUTE_NAME),
            self::MEDIA_FORMAT => new EnumFilter($this->name, AnimeMediaFormat::class, Anime::ATTRIBUTE_MEDIA_FORMAT),
            self::FORMAT => new EnumFilter($this->name, AnimeFormat::class, Anime::ATTRIBUTE_FORMAT),
            self::SEASON => new EnumFilter($this->name, AnimeSeason::class, Anime::ATTRIBUTE_SEASON),
            self::YEAR => new IntFilter($this->name, Anime::ATTRIBUTE_YEAR),
            self::SYNOPSIS => new StringFilter($this->name, Anime::ATTRIBUTE_SYNOPSIS),
            self::CREATED_AT => new TimestampFilter($this->name, Anime::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Anime::ATTRIBUTE_UPDATED_AT),
        };
    }
}

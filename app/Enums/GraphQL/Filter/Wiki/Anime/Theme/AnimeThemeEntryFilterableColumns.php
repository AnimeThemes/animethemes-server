<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki\Anime\Theme;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\BooleanFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

enum AnimeThemeEntryFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case EPISODES;
    case NOTES;
    case NSFW;
    case SPOILER;
    case VERSION;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, AnimeThemeEntry::ATTRIBUTE_ID),
            self::EPISODES => new StringFilter($this->name, AnimeThemeEntry::ATTRIBUTE_EPISODES),
            self::NOTES => new StringFilter($this->name, AnimeThemeEntry::ATTRIBUTE_NOTES),
            self::NSFW => new BooleanFilter($this->name, AnimeThemeEntry::ATTRIBUTE_NSFW),
            self::SPOILER => new BooleanFilter($this->name, AnimeThemeEntry::ATTRIBUTE_SPOILER),
            self::VERSION => new IntFilter($this->name, AnimeThemeEntry::ATTRIBUTE_VERSION),
            self::CREATED_AT => new TimestampFilter($this->name, AnimeThemeEntry::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, AnimeThemeEntry::ATTRIBUTE_UPDATED_AT),
        };
    }
}

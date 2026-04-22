<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki\Anime;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\Wiki\ThemeType;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Anime\AnimeTheme;

enum AnimeThemeFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case TYPE;
    case SEQUENCE;
    case SLUG;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, AnimeTheme::ATTRIBUTE_ID),
            self::TYPE => new EnumFilter($this->name, ThemeType::class, AnimeTheme::ATTRIBUTE_TYPE),
            self::SEQUENCE => new IntFilter($this->name, AnimeTheme::ATTRIBUTE_SEQUENCE),
            self::SLUG => new StringFilter($this->name, AnimeTheme::ATTRIBUTE_SLUG),
            self::CREATED_AT => new TimestampFilter($this->name, AnimeTheme::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, AnimeTheme::ATTRIBUTE_UPDATED_AT),
        };
    }
}

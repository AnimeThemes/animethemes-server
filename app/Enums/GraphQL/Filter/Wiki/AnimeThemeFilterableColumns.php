<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\Wiki\ThemeType;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Theme;

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
            self::ID => new IntFilter($this->name, Theme::ATTRIBUTE_ID),
            self::TYPE => new EnumFilter($this->name, ThemeType::class, Theme::ATTRIBUTE_TYPE),
            self::SEQUENCE => new IntFilter($this->name, Theme::ATTRIBUTE_SEQUENCE),
            self::SLUG => new StringFilter($this->name, Theme::ATTRIBUTE_SLUG),
            self::CREATED_AT => new TimestampFilter($this->name, Theme::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Theme::ATTRIBUTE_UPDATED_AT),
        };
    }
}

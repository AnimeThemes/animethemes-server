<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Song;

enum SongFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case TITLE;
    case TITLE_NATIVE;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Song::ATTRIBUTE_ID),
            self::TITLE => new StringFilter($this->name, Song::ATTRIBUTE_TITLE),
            self::TITLE_NATIVE => new StringFilter($this->name, Song::ATTRIBUTE_TITLE_NATIVE),
            self::CREATED_AT => new TimestampFilter($this->name, Song::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Song::ATTRIBUTE_UPDATED_AT),
        };
    }
}

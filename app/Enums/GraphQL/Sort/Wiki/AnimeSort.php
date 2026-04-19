<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\Wiki\Anime;

enum AnimeSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case NAME;
    case NAME_DESC;
    case YEAR;
    case YEAR_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this, Anime::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this, Anime::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME => new FieldSortCriteria($this, Anime::ATTRIBUTE_NAME, isStringField: true),
            self::NAME_DESC => new FieldSortCriteria($this, Anime::ATTRIBUTE_NAME, SortDirection::DESC, isStringField: true),
            self::YEAR => new FieldSortCriteria($this, Anime::ATTRIBUTE_YEAR),
            self::YEAR_DESC => new FieldSortCriteria($this, Anime::ATTRIBUTE_YEAR, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this, Anime::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Anime::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Anime::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Anime::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return true;
    }
}

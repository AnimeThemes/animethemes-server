<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\Wiki\Song;

enum SongSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case TITLE;
    case TITLE_DESC;
    case TITLE_NATIVE;
    case TITLE_NATIVE_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this, Song::ATTRIBUTE_ID, SortDirection::ASC),
            self::ID_DESC => new FieldSortCriteria($this, Song::ATTRIBUTE_ID, SortDirection::DESC),
            self::TITLE => new FieldSortCriteria($this, Song::ATTRIBUTE_TITLE, isStringField: true),
            self::TITLE_DESC => new FieldSortCriteria($this, Song::ATTRIBUTE_TITLE, SortDirection::DESC, isStringField: true),
            self::TITLE_NATIVE => new FieldSortCriteria($this, Song::ATTRIBUTE_TITLE_NATIVE, isStringField: true),
            self::TITLE_NATIVE_DESC => new FieldSortCriteria($this, Song::ATTRIBUTE_TITLE_NATIVE, SortDirection::DESC, isStringField: true),
            self::CREATED_AT => new FieldSortCriteria($this, Song::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Song::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Song::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Song::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return true;
    }
}

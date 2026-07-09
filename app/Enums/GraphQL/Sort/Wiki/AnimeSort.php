<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\SortCriteria;
use App\Models\Wiki\Anime;
use GraphQL\Type\Definition\Deprecated;

enum AnimeSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case NAME;
    case NAME_DESC;
    case START_DATE;
    case START_DATE_DESC;
    case END_DATE;
    case END_DATE_DESC;
    #[Deprecated('Use the `START_DATE` sort instead.')]
    case YEAR;
    #[Deprecated('Use the `START_DATE_DESC` sort instead.')]
    case YEAR_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_NAME),
            self::NAME_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_NAME, SortDirection::DESC),
            self::YEAR => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_YEAR),
            self::YEAR_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_YEAR, SortDirection::DESC),
            self::START_DATE => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_START_DATE),
            self::START_DATE_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_START_DATE, SortDirection::DESC),
            self::END_DATE => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_END_DATE),
            self::END_DATE_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_END_DATE, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

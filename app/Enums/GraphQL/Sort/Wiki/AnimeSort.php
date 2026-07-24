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
    #[Deprecated("Use 'TITLE' instead")]
    case NAME;
    #[Deprecated("Use 'TITLE_DESC' instead")]
    case NAME_DESC;
    case TITLE_ROMAJI;
    case TITLE_ROMAJI_DESC;
    case TITLE_ENGLISH;
    case TITLE_ENGLISH_DESC;
    case TITLE_NATIVE;
    case TITLE_NATIVE_DESC;
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
            self::ID => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME, self::TITLE_ROMAJI => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_TITLE),
            self::NAME_DESC, self::TITLE_ROMAJI_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_TITLE, SortDirection::DESC),
            self::TITLE_ENGLISH => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_TITLE_ENGLISH),
            self::TITLE_ENGLISH_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_TITLE_ENGLISH, SortDirection::DESC),
            self::TITLE_NATIVE => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_TITLE_NATIVE),
            self::TITLE_NATIVE_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_TITLE_NATIVE, SortDirection::DESC),
            self::YEAR => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_YEAR),
            self::YEAR_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_YEAR, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, Anime::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

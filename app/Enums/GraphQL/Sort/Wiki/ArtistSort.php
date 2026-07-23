<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\SortCriteria;
use App\Models\Wiki\Artist;
use GraphQL\Type\Definition\Deprecated;

enum ArtistSort implements EnumSort
{
    case ID;
    case ID_DESC;
    #[Deprecated('Use NAME_MAIN instead')]
    case NAME;
    #[Deprecated('Use NAME_MAIN_DESC instead')]
    case NAME_DESC;
    case NAME_MAIN;
    case NAME_MAIN_DESC;
    case NAME_NATIVE;
    case NAME_NATIVE_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME, self::NAME_MAIN => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_NAME),
            self::NAME_DESC, self::NAME_MAIN_DESC => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_NAME, SortDirection::DESC),
            self::NAME_NATIVE => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_NAME_NATIVE),
            self::NAME_NATIVE_DESC => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_NAME_NATIVE, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, Artist::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

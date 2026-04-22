<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\SortCriteria;
use App\Models\Wiki\Group;

enum ThemeGroupSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case NAME;
    case NAME_DESC;
    case SLUG;
    case SLUG_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this->name, Group::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, Group::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME => new FieldSortCriteria($this->name, Group::ATTRIBUTE_NAME, isStringField: true),
            self::NAME_DESC => new FieldSortCriteria($this->name, Group::ATTRIBUTE_NAME, SortDirection::DESC, isStringField: true),
            self::SLUG => new FieldSortCriteria($this->name, Group::ATTRIBUTE_SLUG, isStringField: true),
            self::SLUG_DESC => new FieldSortCriteria($this->name, Group::ATTRIBUTE_SLUG, SortDirection::DESC, isStringField: true),
            self::CREATED_AT => new FieldSortCriteria($this->name, Group::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, Group::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, Group::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, Group::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

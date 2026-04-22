<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki\Song;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\SortCriteria;
use App\Models\Wiki\Song\Performance;

enum PerformanceSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case ALIAS;
    case ALIAS_DESC;
    case AS;
    case AS_DESC;
    case MEMBER_ALIAS;
    case MEMBER_ALIAS_DESC;
    case MEMBER_AS;
    case MEMBER_AS_DESC;
    case RELEVANCE;
    case RELEVANCE_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_ID, SortDirection::DESC),
            self::ALIAS => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_ALIAS, isStringField: true),
            self::ALIAS_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_ALIAS, SortDirection::DESC, isStringField: true),
            self::AS => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_AS, isStringField: true),
            self::AS_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_AS, SortDirection::DESC, isStringField: true),
            self::MEMBER_ALIAS => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_MEMBER_ALIAS, isStringField: true),
            self::MEMBER_ALIAS_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_MEMBER_ALIAS, SortDirection::DESC, isStringField: true),
            self::MEMBER_AS => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_MEMBER_AS, isStringField: true),
            self::MEMBER_AS_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_MEMBER_AS, SortDirection::DESC, isStringField: true),
            self::RELEVANCE => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_RELEVANCE),
            self::RELEVANCE_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_RELEVANCE, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, Performance::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

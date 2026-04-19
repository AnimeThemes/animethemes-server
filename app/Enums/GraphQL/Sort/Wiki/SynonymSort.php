<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\Wiki\Synonym;

enum SynonymSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case TEXT;
    case TEXT_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this, Synonym::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this, Synonym::ATTRIBUTE_ID, SortDirection::DESC),
            self::TEXT => new FieldSortCriteria($this, Synonym::ATTRIBUTE_TEXT, isStringField: true),
            self::TEXT_DESC => new FieldSortCriteria($this, Synonym::ATTRIBUTE_TEXT, SortDirection::DESC, isStringField: true),
            self::CREATED_AT => new FieldSortCriteria($this, Synonym::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Synonym::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Synonym::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Synonym::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return true;
    }
}

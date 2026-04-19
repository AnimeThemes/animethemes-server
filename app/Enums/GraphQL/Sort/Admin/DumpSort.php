<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Admin;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\Admin\Dump;

enum DumpSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this, Dump::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this, Dump::ATTRIBUTE_ID, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this, Dump::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Dump::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Dump::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Dump::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return true;
    }
}

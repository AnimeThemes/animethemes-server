<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Document;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\Document\Page;

enum PageSort implements EnumSort
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
            self::ID => new FieldSortCriteria($this, Page::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this, Page::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME => new FieldSortCriteria($this, Page::ATTRIBUTE_NAME, isStringField: true),
            self::NAME_DESC => new FieldSortCriteria($this, Page::ATTRIBUTE_NAME, SortDirection::DESC, isStringField: true),
            self::SLUG => new FieldSortCriteria($this, Page::ATTRIBUTE_SLUG, isStringField: true),
            self::SLUG_DESC => new FieldSortCriteria($this, Page::ATTRIBUTE_SLUG, SortDirection::DESC, isStringField: true),
            self::CREATED_AT => new FieldSortCriteria($this, Page::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Page::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Page::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Page::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return true;
    }
}

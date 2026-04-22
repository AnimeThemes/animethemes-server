<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\SortCriteria;
use App\Models\Wiki\ExternalResource;

enum ExternalResourceSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case SITE;
    case SITE_DESC;
    case EXTERNAL_ID;
    case EXTERNAL_ID_DESC;
    case LINK;
    case LINK_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_ID, SortDirection::ASC),
            self::ID_DESC => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_ID, SortDirection::DESC),
            self::SITE => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_SITE),
            self::SITE_DESC => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_SITE, SortDirection::DESC),
            self::EXTERNAL_ID => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_EXTERNAL_ID),
            self::EXTERNAL_ID_DESC => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_EXTERNAL_ID, SortDirection::DESC),
            self::LINK => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_LINK, isStringField: true),
            self::LINK_DESC => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_LINK, SortDirection::DESC, isStringField: true),
            self::CREATED_AT => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, ExternalResource::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

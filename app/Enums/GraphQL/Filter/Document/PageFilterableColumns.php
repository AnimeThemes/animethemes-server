<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Document;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Document\Page;

enum PageFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case NAME;
    case SLUG;
    case BODY;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Page::ATTRIBUTE_ID),
            self::NAME => new StringFilter($this->name, Page::ATTRIBUTE_NAME),
            self::SLUG => new StringFilter($this->name, Page::ATTRIBUTE_SLUG),
            self::BODY => new StringFilter($this->name, Page::ATTRIBUTE_BODY),
            self::CREATED_AT => new TimestampFilter($this->name, Page::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Page::ATTRIBUTE_UPDATED_AT),
        };
    }
}

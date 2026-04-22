<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Series;

enum SeriesFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case NAME;
    case SLUG;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Series::ATTRIBUTE_ID),
            self::NAME => new StringFilter($this->name, Series::ATTRIBUTE_NAME),
            self::SLUG => new StringFilter($this->name, Series::ATTRIBUTE_SLUG),
            self::CREATED_AT => new TimestampFilter($this->name, Series::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Series::ATTRIBUTE_UPDATED_AT),
        };
    }
}

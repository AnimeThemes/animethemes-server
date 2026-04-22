<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki\Song;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Song\Performance;

enum PerformanceFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case ALIAS;
    case AS;
    case MEMBER_ALIAS;
    case MEMBER_AS;
    case RELEVANCE;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Performance::ATTRIBUTE_ID),
            self::ALIAS => new StringFilter($this->name, Performance::ATTRIBUTE_ALIAS),
            self::AS => new StringFilter($this->name, Performance::ATTRIBUTE_AS),
            self::MEMBER_ALIAS => new StringFilter($this->name, Performance::ATTRIBUTE_MEMBER_ALIAS),
            self::MEMBER_AS => new StringFilter($this->name, Performance::ATTRIBUTE_MEMBER_AS),
            self::RELEVANCE => new IntFilter($this->name, Performance::ATTRIBUTE_RELEVANCE),
            self::CREATED_AT => new TimestampFilter($this->name, Performance::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Performance::ATTRIBUTE_UPDATED_AT),
        };
    }
}

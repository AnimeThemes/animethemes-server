<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Synonym;

enum SynonymFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case TEXT;
    case LANGUAGE;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Synonym::ATTRIBUTE_ID),
            self::TEXT => new StringFilter($this->name, Synonym::ATTRIBUTE_TEXT),
            self::LANGUAGE => new StringFilter($this->name, Synonym::ATTRIBUTE_LANGUAGE),
            self::CREATED_AT => new TimestampFilter($this->name, Synonym::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Synonym::ATTRIBUTE_UPDATED_AT),
        };
    }
}

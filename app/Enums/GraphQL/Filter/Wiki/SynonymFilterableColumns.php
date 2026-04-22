<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\Wiki\SynonymType;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Synonym;

enum SynonymFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case TEXT;
    case TYPE;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Synonym::ATTRIBUTE_ID),
            self::TEXT => new StringFilter($this->name, Synonym::ATTRIBUTE_TEXT),
            self::TYPE => new EnumFilter($this->name, SynonymType::class, Synonym::ATTRIBUTE_TYPE),
            self::CREATED_AT => new TimestampFilter($this->name, Synonym::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Synonym::ATTRIBUTE_UPDATED_AT),
        };
    }
}

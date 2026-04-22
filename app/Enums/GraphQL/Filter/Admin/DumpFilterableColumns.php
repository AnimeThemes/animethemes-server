<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Admin;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Admin\Dump;

enum DumpFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case PATH;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Dump::ATTRIBUTE_ID),
            self::PATH => new StringFilter($this->name, Dump::ATTRIBUTE_PATH),
            self::CREATED_AT => new TimestampFilter($this->name, Dump::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Dump::ATTRIBUTE_UPDATED_AT),
        };
    }
}

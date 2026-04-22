<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\Wiki\ResourceSite;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\ExternalResource;

enum ExternalResourceFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case EXTERNAL_ID;
    case LINK;
    case SITE;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, ExternalResource::ATTRIBUTE_ID),
            self::EXTERNAL_ID => new IntFilter($this->name, ExternalResource::ATTRIBUTE_EXTERNAL_ID),
            self::LINK => new StringFilter($this->name, ExternalResource::ATTRIBUTE_LINK),
            self::SITE => new EnumFilter($this->name, ResourceSite::class, ExternalResource::ATTRIBUTE_SITE),
            self::CREATED_AT => new TimestampFilter($this->name, ExternalResource::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, ExternalResource::ATTRIBUTE_UPDATED_AT),
        };
    }
}

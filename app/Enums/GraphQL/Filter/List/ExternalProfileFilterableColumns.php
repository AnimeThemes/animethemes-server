<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\List;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\List\ExternalProfile;

enum ExternalProfileFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case NAME;
    case SITE;
    case VISIBILITY;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, ExternalProfile::ATTRIBUTE_ID),
            self::NAME => new StringFilter($this->name, ExternalProfile::ATTRIBUTE_NAME),
            self::SITE => new EnumFilter($this->name, ExternalProfileSite::class, ExternalProfile::ATTRIBUTE_SITE),
            self::VISIBILITY => new EnumFilter($this->name, ExternalProfileVisibility::class, ExternalProfile::ATTRIBUTE_VISIBILITY),
            self::CREATED_AT => new TimestampFilter($this->name, ExternalProfile::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, ExternalProfile::ATTRIBUTE_UPDATED_AT),
        };
    }
}

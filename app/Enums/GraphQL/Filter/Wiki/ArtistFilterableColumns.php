<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Artist;
use GraphQL\Type\Definition\Deprecated;

enum ArtistFilterableColumns implements EnumFilterableColumns
{
    case ID;
    #[Deprecated('Use NAME_MAIN instead')]
    case NAME;
    case NAME_MAIN;
    case NAME_NATIVE;
    case SLUG;
    case INFORMATION;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Artist::ATTRIBUTE_ID),
            self::NAME, self::NAME_MAIN => new StringFilter($this->name, Artist::ATTRIBUTE_NAME),
            self::NAME_NATIVE => new StringFilter($this->name, Artist::ATTRIBUTE_NAME_NATIVE),
            self::SLUG => new StringFilter($this->name, Artist::ATTRIBUTE_SLUG),
            self::INFORMATION => new StringFilter($this->name, Artist::ATTRIBUTE_INFORMATION),
            self::CREATED_AT => new TimestampFilter($this->name, Artist::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Artist::ATTRIBUTE_UPDATED_AT),
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\Wiki\ImageFacet;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Image;

enum ImageFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case FACET;
    case PATH;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Image::ATTRIBUTE_ID),
            self::FACET => new EnumFilter($this->name, ImageFacet::class, Image::ATTRIBUTE_FACET),
            self::PATH => new StringFilter($this->name, Image::ATTRIBUTE_PATH),
            self::CREATED_AT => new TimestampFilter($this->name, Image::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Image::ATTRIBUTE_UPDATED_AT),
        };
    }
}

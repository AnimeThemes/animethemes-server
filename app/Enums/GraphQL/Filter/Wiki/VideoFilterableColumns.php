<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\GraphQL\Filter\BooleanFilter;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Video;

enum VideoFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case BASENAME;
    case FILENAME;
    case LYRICS;
    case MIMETYPE;
    case NC;
    case OVERLAP;
    case PATH;
    case RESOLUTION;
    case SIZE;
    case SOURCE;
    case SUBBED;
    case UNCEN;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Video::ATTRIBUTE_ID),
            self::BASENAME => new StringFilter($this->name, Video::ATTRIBUTE_BASENAME),
            self::FILENAME => new StringFilter($this->name, Video::ATTRIBUTE_FILENAME),
            self::LYRICS => new BooleanFilter($this->name, Video::ATTRIBUTE_LYRICS),
            self::MIMETYPE => new StringFilter($this->name, Video::ATTRIBUTE_MIMETYPE),
            self::NC => new BooleanFilter($this->name, Video::ATTRIBUTE_NC),
            self::OVERLAP => new EnumFilter($this->name, VideoOverlap::class, Video::ATTRIBUTE_OVERLAP),
            self::PATH => new StringFilter($this->name, Video::ATTRIBUTE_PATH),
            self::RESOLUTION => new IntFilter($this->name, Video::ATTRIBUTE_RESOLUTION),
            self::SIZE => new IntFilter($this->name, Video::ATTRIBUTE_SIZE),
            self::SOURCE => new EnumFilter($this->name, VideoSource::class, Video::ATTRIBUTE_SOURCE),
            self::SUBBED => new BooleanFilter($this->name, Video::ATTRIBUTE_SUBBED),
            self::UNCEN => new BooleanFilter($this->name, Video::ATTRIBUTE_UNCEN),
            self::CREATED_AT => new TimestampFilter($this->name, Video::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Video::ATTRIBUTE_UPDATED_AT),
        };
    }
}

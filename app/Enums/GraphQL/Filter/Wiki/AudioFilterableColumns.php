<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Wiki;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Wiki\Audio;

enum AudioFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case BASENAME;
    case FILENAME;
    case MIMETYPE;
    case SIZE;
    case PATH;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Audio::ATTRIBUTE_ID),
            self::BASENAME => new StringFilter($this->name, Audio::ATTRIBUTE_BASENAME),
            self::FILENAME => new StringFilter($this->name, Audio::ATTRIBUTE_FILENAME),
            self::MIMETYPE => new StringFilter($this->name, Audio::ATTRIBUTE_MIMETYPE),
            self::SIZE => new IntFilter($this->name, Audio::ATTRIBUTE_SIZE),
            self::PATH => new StringFilter($this->name, Audio::ATTRIBUTE_PATH),
            self::CREATED_AT => new TimestampFilter($this->name, Audio::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Audio::ATTRIBUTE_UPDATED_AT),
        };
    }
}

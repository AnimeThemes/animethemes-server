<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\List;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Filter\EnumFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\List\Playlist;

enum PlaylistFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case NAME;
    case VISIBILITY;
    case DESCRIPTION;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Playlist::ATTRIBUTE_ID),
            self::NAME => new StringFilter($this->name, Playlist::ATTRIBUTE_NAME),
            self::VISIBILITY => new EnumFilter($this->name, PlaylistVisibility::class, Playlist::ATTRIBUTE_VISIBILITY),
            self::DESCRIPTION => new StringFilter($this->name, Playlist::ATTRIBUTE_DESCRIPTION),
            self::CREATED_AT => new TimestampFilter($this->name, Playlist::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Playlist::ATTRIBUTE_UPDATED_AT),
        };
    }
}

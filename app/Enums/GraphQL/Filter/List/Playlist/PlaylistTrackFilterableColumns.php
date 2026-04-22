<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\List\Playlist;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\List\Playlist\PlaylistTrack;

enum PlaylistTrackFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case POSITION;
    case ENTRY_ID;
    case VIDEO_ID;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, PlaylistTrack::ATTRIBUTE_ID),
            self::POSITION => new IntFilter($this->name, PlaylistTrack::ATTRIBUTE_POSITION),
            self::ENTRY_ID => new IntFilter($this->name, PlaylistTrack::ATTRIBUTE_ENTRY),
            self::VIDEO_ID => new IntFilter($this->name, PlaylistTrack::ATTRIBUTE_VIDEO),
            self::CREATED_AT => new TimestampFilter($this->name, PlaylistTrack::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, PlaylistTrack::ATTRIBUTE_UPDATED_AT),
        };
    }
}

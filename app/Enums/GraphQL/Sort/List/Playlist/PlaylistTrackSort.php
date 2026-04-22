<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\List\Playlist;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\SortCriteria;
use App\Models\List\Playlist\PlaylistTrack;

enum PlaylistTrackSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case POSITION;
    case POSITION_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_ID, SortDirection::DESC),
            self::POSITION => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_POSITION),
            self::POSITION_DESC => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_POSITION, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, PlaylistTrack::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

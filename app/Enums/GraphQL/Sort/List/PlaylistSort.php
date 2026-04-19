<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\List;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\List\Playlist;

enum PlaylistSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case NAME;
    case NAME_DESC;
    case LIKES_COUNT;
    case LIKES_COUNT_DESC;
    case TRACKS_COUNT;
    case TRACKS_COUNT_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this, Playlist::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this, Playlist::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME => new FieldSortCriteria($this, Playlist::ATTRIBUTE_NAME, isStringField: true),
            self::NAME_DESC => new FieldSortCriteria($this, Playlist::ATTRIBUTE_NAME, SortDirection::DESC, isStringField: true),
            self::LIKES_COUNT => new FieldSortCriteria($this, 'like_aggregate_sum_value'),
            self::LIKES_COUNT_DESC => new FieldSortCriteria($this, 'like_aggregate_sum_value', SortDirection::DESC),
            self::TRACKS_COUNT => new FieldSortCriteria($this, 'tracksCount'),
            self::TRACKS_COUNT_DESC => new FieldSortCriteria($this, 'tracksCount', SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this, Playlist::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Playlist::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Playlist::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Playlist::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return match ($this) {
            self::LIKES_COUNT,
            self::LIKES_COUNT_DESC,
            self::TRACKS_COUNT,
            self::TRACKS_COUNT_DESC => false,
            default => true,
        };
    }
}

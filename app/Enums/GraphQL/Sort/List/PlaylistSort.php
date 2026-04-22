<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\List;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\QualifyColumn;
use App\Enums\GraphQL\SortDirection;
use App\Enums\Http\Api\Field\AggregateFunction;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\SortCriteria;
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
            self::ID => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_NAME, isStringField: true),
            self::NAME_DESC => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_NAME, SortDirection::DESC, isStringField: true),
            self::LIKES_COUNT => new FieldSortCriteria($this->name, 'like_aggregate_sum_value', qualifyColumn: QualifyColumn::NO),
            self::LIKES_COUNT_DESC => new FieldSortCriteria($this->name, 'like_aggregate_sum_value', SortDirection::DESC, qualifyColumn: QualifyColumn::NO),
            self::TRACKS_COUNT => new FieldSortCriteria($this->name, 'tracks_count', qualifyColumn: QualifyColumn::NO)->setAggregateRelation(Playlist::RELATION_TRACKS, AggregateFunction::COUNT),
            self::TRACKS_COUNT_DESC => new FieldSortCriteria($this->name, 'tracks_count', SortDirection::DESC, qualifyColumn: QualifyColumn::NO)->setAggregateRelation(Playlist::RELATION_TRACKS, AggregateFunction::COUNT),
            self::CREATED_AT => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, Playlist::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

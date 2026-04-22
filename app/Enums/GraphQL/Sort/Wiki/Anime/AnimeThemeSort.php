<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki\Anime;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Sort\FieldSortCriteria;
use App\GraphQL\Sort\RandomSortCriteria;
use App\GraphQL\Sort\RelationSortCriteria;
use App\GraphQL\Sort\SortCriteria;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Song;

enum AnimeThemeSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case SEQUENCE;
    case SEQUENCE_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case SONG_TITLE;
    case SONG_TITLE_DESC;
    case SONG_TITLE_NATIVE;
    case SONG_TITLE_NATIVE_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_ID, SortDirection::DESC),
            self::SEQUENCE => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_SEQUENCE),
            self::SEQUENCE_DESC => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_SEQUENCE, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this->name, AnimeTheme::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::SONG_TITLE => new RelationSortCriteria($this->name, Song::ATTRIBUTE_TITLE, AnimeTheme::RELATION_SONG, isStringField: true),
            self::SONG_TITLE_DESC => new RelationSortCriteria($this->name, Song::ATTRIBUTE_TITLE, AnimeTheme::RELATION_SONG, SortDirection::DESC, isStringField: true),
            self::SONG_TITLE_NATIVE => new RelationSortCriteria($this->name, Song::ATTRIBUTE_TITLE_NATIVE, AnimeTheme::RELATION_SONG, isStringField: true),
            self::SONG_TITLE_NATIVE_DESC => new RelationSortCriteria($this->name, Song::ATTRIBUTE_TITLE_NATIVE, AnimeTheme::RELATION_SONG, SortDirection::DESC, isStringField: true),
            self::RANDOM => new RandomSortCriteria($this->name, ''),
        };
    }
}

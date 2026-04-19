<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Wiki;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\Wiki\Audio;

enum AudioSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case BASENAME;
    case BASENAME_DESC;
    case FILENAME;
    case FILENAME_DESC;
    case SIZE;
    case SIZE_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this, Audio::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this, Audio::ATTRIBUTE_ID, SortDirection::DESC),
            self::BASENAME => new FieldSortCriteria($this, Audio::ATTRIBUTE_BASENAME, isStringField: true),
            self::BASENAME_DESC => new FieldSortCriteria($this, Audio::ATTRIBUTE_BASENAME, SortDirection::DESC, isStringField: true),
            self::FILENAME => new FieldSortCriteria($this, Audio::ATTRIBUTE_FILENAME, isStringField: true),
            self::FILENAME_DESC => new FieldSortCriteria($this, Audio::ATTRIBUTE_FILENAME, SortDirection::DESC, isStringField: true),
            self::SIZE => new FieldSortCriteria($this, Audio::ATTRIBUTE_SIZE),
            self::SIZE_DESC => new FieldSortCriteria($this, Audio::ATTRIBUTE_SIZE, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this, Audio::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Audio::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Audio::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Audio::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return true;
    }
}

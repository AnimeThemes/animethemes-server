<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort\Pivot;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\PivotSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;

enum ArtistMemberSort implements EnumSort
{
    case ID;
    case ID_DESC;
    case NAME;
    case NAME_DESC;
    case MEMBER_ALIAS;
    case MEMBER_ALIAS_DESC;
    case MEMBER_AS;
    case MEMBER_AS_DESC;
    case MEMBER_RELEVANCE;
    case MEMBER_RELEVANCE_DESC;
    case CREATED_AT;
    case CREATED_AT_DESC;
    case UPDATED_AT;
    case UPDATED_AT_DESC;
    case RANDOM;

    public function getSortCriteria(): SortCriteria
    {
        return match ($this) {
            self::ID => new FieldSortCriteria($this, Artist::ATTRIBUTE_ID),
            self::ID_DESC => new FieldSortCriteria($this, Artist::ATTRIBUTE_ID, SortDirection::DESC),
            self::NAME => new FieldSortCriteria($this, Artist::ATTRIBUTE_NAME, isStringField: true),
            self::NAME_DESC => new FieldSortCriteria($this, Artist::ATTRIBUTE_NAME, SortDirection::DESC, isStringField: true),
            self::MEMBER_ALIAS => new PivotSortCriteria($this, ArtistMember::ATTRIBUTE_ALIAS, isStringField: true),
            self::MEMBER_ALIAS_DESC => new PivotSortCriteria($this, ArtistMember::ATTRIBUTE_ALIAS, SortDirection::DESC, isStringField: true),
            self::MEMBER_AS => new PivotSortCriteria($this, ArtistMember::ATTRIBUTE_AS, isStringField: true),
            self::MEMBER_AS_DESC => new PivotSortCriteria($this, ArtistMember::ATTRIBUTE_AS, SortDirection::DESC, isStringField: true),
            self::MEMBER_RELEVANCE => new PivotSortCriteria($this, ArtistMember::ATTRIBUTE_RELEVANCE),
            self::MEMBER_RELEVANCE_DESC => new PivotSortCriteria($this, ArtistMember::ATTRIBUTE_RELEVANCE, SortDirection::DESC),
            self::CREATED_AT => new FieldSortCriteria($this, Artist::ATTRIBUTE_CREATED_AT),
            self::CREATED_AT_DESC => new FieldSortCriteria($this, Artist::ATTRIBUTE_CREATED_AT, SortDirection::DESC),
            self::UPDATED_AT => new FieldSortCriteria($this, Artist::ATTRIBUTE_UPDATED_AT),
            self::UPDATED_AT_DESC => new FieldSortCriteria($this, Artist::ATTRIBUTE_UPDATED_AT, SortDirection::DESC),
            self::RANDOM => new RandomSortCriteria($this, ''),
        };
    }

    public function shouldQualifyColumn(): bool
    {
        return true;
    }
}

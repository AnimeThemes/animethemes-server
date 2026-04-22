<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter\Admin;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Filter\StringFilter;
use App\GraphQL\Filter\TimestampFilter;
use App\Models\Admin\Announcement;

enum AnnouncementFilterableColumns implements EnumFilterableColumns
{
    case ID;
    case CONTENT;
    case CREATED_AT;
    case UPDATED_AT;

    public function getFilter(): Filter
    {
        return match ($this) {
            self::ID => new IntFilter($this->name, Announcement::ATTRIBUTE_ID),
            self::CONTENT => new StringFilter($this->name, Announcement::ATTRIBUTE_CONTENT),
            self::CREATED_AT => new TimestampFilter($this->name, Announcement::ATTRIBUTE_CREATED_AT),
            self::UPDATED_AT => new TimestampFilter($this->name, Announcement::ATTRIBUTE_UPDATED_AT),
        };
    }
}

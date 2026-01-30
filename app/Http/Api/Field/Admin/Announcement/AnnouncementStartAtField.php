<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Announcement;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Http\Api\Field\DateField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementStartAtField extends DateField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Announcement::ATTRIBUTE_START_AT);
    }

    public function shouldRender(Query $query): bool
    {
        return false;
    }

    public function getCreationRules(Request $request): array
    {
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return [
            'required',
            'before:'.Announcement::ATTRIBUTE_END_AT,
            Str::of('date_format:')
                ->append(implode(',', $allowedDateFormats))
                ->__toString(),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return [
            'sometimes',
            'required',
            'before:'.Announcement::ATTRIBUTE_END_AT,
            Str::of('date_format:')
                ->append(implode(',', $allowedDateFormats))
                ->__toString(),
        ];
    }
}

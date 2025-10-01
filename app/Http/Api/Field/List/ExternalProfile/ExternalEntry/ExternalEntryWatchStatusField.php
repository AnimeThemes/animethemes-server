<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile\ExternalEntry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\List\External\ExternalEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ExternalEntryWatchStatusField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalEntry::ATTRIBUTE_WATCH_STATUS, ExternalEntryWatchStatus::class);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new Enum(ExternalEntryWatchStatus::class),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(ExternalEntryWatchStatus::class),
        ];
    }
}

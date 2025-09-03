<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalEntry;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\List\External\ExternalEntry;

class ExternalEntryWatchStatusField extends EnumField
{
    public function __construct()
    {
        parent::__construct(ExternalEntry::ATTRIBUTE_WATCH_STATUS, ExternalEntryWatchStatus::class, nullable: false);
    }

    public function description(): string
    {
        return 'The watch status of the entry on the external site';
    }
}

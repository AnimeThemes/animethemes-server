<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalEntry;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\List\External\ExternalEntry;

class ExternalEntryWatchStatusField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalEntry::ATTRIBUTE_WATCH_STATUS, ExternalEntryWatchStatus::class, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The watch status of the entry on the external site';
    }
}

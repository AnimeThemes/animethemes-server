<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalEntry;

use App\Enums\Models\List\ExternalEntryStatus;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\List\External\ExternalEntry;

class ExternalEntryStatusField extends EnumField
{
    public function __construct()
    {
        parent::__construct(ExternalEntry::ATTRIBUTE_STATUS, ExternalEntryStatus::class, nullable: false);
    }

    public function description(): string
    {
        return 'The status of the entry on the external site';
    }
}

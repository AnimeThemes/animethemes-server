<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\ExternalProfile\ExternalEntry;

use App\GraphQL\Schema\Fields\BooleanField;
use App\Models\List\External\ExternalEntry;

class ExternalEntryIsFavoriteField extends BooleanField
{
    public function __construct()
    {
        parent::__construct(ExternalEntry::ATTRIBUTE_IS_FAVORITE);
    }

    public function description(): string
    {
        return 'The favorite state of the entry on the external site';
    }
}

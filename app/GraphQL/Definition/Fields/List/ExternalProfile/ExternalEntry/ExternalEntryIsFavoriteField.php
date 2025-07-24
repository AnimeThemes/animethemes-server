<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalEntry;

use App\GraphQL\Definition\Fields\BooleanField;
use App\Models\List\External\ExternalEntry;

class ExternalEntryIsFavoriteField extends BooleanField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalEntry::ATTRIBUTE_IS_FAVORITE);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The favorite state of the entry on the external site';
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalEntry;

use App\GraphQL\Definition\Fields\FloatField;
use App\Models\List\External\ExternalEntry;

/**
 * Class ExternalEntryScoreField.
 */
class ExternalEntryScoreField extends FloatField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalEntry::ATTRIBUTE_SCORE);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The score of the entry on the external site';
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile\ExternalEntry;

use App\GraphQL\Definition\Fields\FloatField;
use App\Models\List\External\ExternalEntry;

class ExternalEntryScoreField extends FloatField
{
    public function __construct()
    {
        parent::__construct(ExternalEntry::ATTRIBUTE_SCORE);
    }

    public function description(): string
    {
        return 'The score of the entry on the external site';
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Audio;

use App\GraphQL\Definition\Fields\Base\CountAggregateField;
use App\Models\Wiki\Audio;

class AudioViewsCountField extends CountAggregateField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Audio::RELATION_VIEW_AGGREGATE, 'viewsCount');
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The number of views recorded for the resource';
    }
}

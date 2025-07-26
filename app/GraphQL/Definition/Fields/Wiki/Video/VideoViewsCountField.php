<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Attributes\Deprecated;
use App\GraphQL\Definition\Fields\Base\CountAggregateField;
use App\Models\Wiki\Video;

#[Deprecated('We will no longer track views. Use likesCount instead.')]
class VideoViewsCountField extends CountAggregateField
{
    public function __construct()
    {
        parent::__construct(Video::RELATION_VIEW_AGGREGATE, 'viewsCount');
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The number of views recorded for the resource';
    }
}

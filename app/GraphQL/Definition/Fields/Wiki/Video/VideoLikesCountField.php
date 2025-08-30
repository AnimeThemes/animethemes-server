<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Definition\Fields\Base\CountAggregateField;
use App\Models\Wiki\Video;

class VideoLikesCountField extends CountAggregateField
{
    public function __construct()
    {
        parent::__construct(Video::RELATION_LIKE_AGGREGATE, 'likesCount');
    }

    public function description(): string
    {
        return 'The number of likes recorded for the resource';
    }
}

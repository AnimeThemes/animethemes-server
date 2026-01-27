<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base\Aggregate;

use App\Contracts\Models\HasAggregateLikes;

class LikesCountField extends CountAggregateField
{
    public function __construct()
    {
        parent::__construct(HasAggregateLikes::RELATION_LIKE_AGGREGATE, 'likesCount');
    }

    public function description(): string
    {
        return 'The number of likes recorded for the resource';
    }
}

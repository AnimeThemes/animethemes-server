<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Audio;

use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Schema\Fields\Base\CountAggregateField;
use App\Models\Wiki\Audio;

class AudioViewsCountField extends CountAggregateField implements DeprecatedField
{
    public function __construct()
    {
        parent::__construct(Audio::RELATION_VIEW_AGGREGATE, 'viewsCount');
    }

    public function description(): string
    {
        return 'The number of views recorded for the resource';
    }

    public function deprecationReason(): string
    {
        return 'We are no longer tracking views.';
    }
}

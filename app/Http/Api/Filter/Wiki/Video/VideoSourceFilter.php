<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Video;

use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class VideoSourceFilter.
 */
class VideoSourceFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'source', VideoSource::class);
    }
}

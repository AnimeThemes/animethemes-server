<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Video;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class VideoOverlapFilter.
 */
class VideoOverlapFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'overlap', VideoOverlap::class);
    }
}

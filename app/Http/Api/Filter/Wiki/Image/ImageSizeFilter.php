<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Image;

use App\Http\Api\Filter\IntFilter;
use Illuminate\Support\Collection;

/**
 * Class ImageSizeFilter.
 */
class ImageSizeFilter extends IntFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'size');
    }
}

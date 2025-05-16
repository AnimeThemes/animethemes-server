<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Aggregate\LikeAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Interface HasAggregateLikes.
 *
 * @property LikeAggregate|null $likeAggregate
 */
interface HasAggregateLikes
{
    /**
     * Get the likes count of the model.
     *
     * @return MorphOne
     */
    public function likeAggregate(): MorphOne;
}

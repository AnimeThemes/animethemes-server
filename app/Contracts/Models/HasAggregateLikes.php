<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Aggregate\LikeAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property LikeAggregate|null $likeAggregate
 */
interface HasAggregateLikes
{
    /**
     * @return MorphOne
     */
    public function likeAggregate(): MorphOne;
}

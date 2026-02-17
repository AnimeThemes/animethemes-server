<?php

declare(strict_types=1);

namespace App\Concerns\Models\Aggregate;

use App\Models\Aggregate\LikeAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait AggregatesLike
{
    /**
     * @return MorphOne<LikeAggregate, $this>
     */
    public function likeAggregate(): MorphOne
    {
        return $this->morphOne(LikeAggregate::class, LikeAggregate::ATTRIBUTE_LIKEABLE);
    }
}

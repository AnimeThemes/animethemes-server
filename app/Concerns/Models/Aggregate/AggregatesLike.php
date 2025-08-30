<?php

declare(strict_types=1);

namespace App\Concerns\Models\Aggregate;

use App\Models\Aggregate\LikeAggregate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait AggregatesLike
{
    final public const RELATION_LIKE_AGGREGATE = 'likeAggregate';

    /**
     * @return MorphOne<LikeAggregate, $this>
     */
    public function likeAggregate(): MorphOne
    {
        return $this->morphOne(
            LikeAggregate::class,
            LikeAggregate::ATTRIBUTE_LIKEABLE,
        );
    }
}

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
    public const RELATION_LIKE_AGGREGATE = 'likeAggregate';

    public function likeAggregate(): MorphOne;
}

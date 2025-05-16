<?php

declare(strict_types=1);

namespace App\Models\Aggregate;

use App\Concerns\Models\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class LikeAggregate.
 *
 * @property Likeable $likeable
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $value
 */
class LikeAggregate extends Model
{
    final public const TABLE = 'like_aggregates';

    final public const ATTRIBUTE_LIKEABLE = 'likeable';
    final public const ATTRIBUTE_LIKEABLE_TYPE = 'likeable_type';
    final public const ATTRIBUTE_LIKEABLE_ID = 'likeable_id';
    final public const ATTRIBUTE_VALUE = 'value';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = LikeAggregate::TABLE;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the likeable of the aggregate.
     *
     * @return MorphTo
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Aggregate;

use App\Contracts\Models\Likeable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Likeable $likeable
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $value
 */
#[Table(LikeAggregate::TABLE, incrementing: false, timestamps: false)]
class LikeAggregate extends Model
{
    final public const string TABLE = 'like_aggregates';

    final public const string ATTRIBUTE_LIKEABLE = 'likeable';
    final public const string ATTRIBUTE_LIKEABLE_TYPE = 'likeable_type';
    final public const string ATTRIBUTE_LIKEABLE_ID = 'likeable_id';
    final public const string ATTRIBUTE_VALUE = 'value';

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Aggregate;

use App\Contracts\Models\Likeable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Likeable $likeable
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $value
 */
#[Table(LikeAggregate::TABLE)]
#[WithoutIncrementing]
#[WithoutTimestamps]
class LikeAggregate extends Model
{
    final public const string TABLE = 'like_aggregates';

    final public const string ATTRIBUTE_LIKEABLE = 'likeable';
    final public const string ATTRIBUTE_LIKEABLE_TYPE = 'likeable_type';
    final public const string ATTRIBUTE_LIKEABLE_ID = 'likeable_id';
    final public const string ATTRIBUTE_VALUE = 'value';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            LikeAggregate::ATTRIBUTE_LIKEABLE_TYPE => 'string',
            LikeAggregate::ATTRIBUTE_LIKEABLE_ID => 'int',
            LikeAggregate::ATTRIBUTE_VALUE => 'int',
        ];
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}

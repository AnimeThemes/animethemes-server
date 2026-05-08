<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Enums\Models\Admin\ActivityStatus;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity as BaseActivity;

/**
 * @property ActivityStatus|null $status
 */
class Activity extends BaseActivity
{
    final public const string TABLE = 'activity_log';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_EXCEPTION = 'exception';
    final public const string ATTRIBUTE_FINISHED_AT = 'finished_at';
    final public const string ATTRIBUTE_NAME = 'event';
    final public const string ATTRIBUTE_PROPERTIES = 'properties';
    final public const string ATTRIBUTE_RELATED_ID = 'related_id';
    final public const string ATTRIBUTE_STATUS = 'status';

    final public const string RELATION_RELATED = 'related';
    final public const string RELATION_USER = 'causer';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ...parent::casts(),
            Activity::ATTRIBUTE_FINISHED_AT => 'datetime',
            Activity::ATTRIBUTE_STATUS => ActivityStatus::class,
        ];
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}

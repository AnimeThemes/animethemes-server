<?php

declare(strict_types=1);

namespace App\Models\Aggregate;

use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Viewable $viewable
 * @property string $viewable_type
 * @property int $viewable_id
 * @property int $value
 */
class ViewAggregate extends Model
{
    final public const TABLE = 'view_aggregates';

    final public const ATTRIBUTE_VIEWABLE = 'viewable';
    final public const ATTRIBUTE_VIEWABLE_TYPE = 'viewable_type';
    final public const ATTRIBUTE_VIEWABLE_ID = 'viewable_id';
    final public const ATTRIBUTE_VALUE = 'value';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ViewAggregate::TABLE;

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
     * @return MorphTo
     */
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }
}

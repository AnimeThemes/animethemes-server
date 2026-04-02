<?php

declare(strict_types=1);

namespace App\Pivots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
abstract class BasePivot extends Pivot
{
    use HasFactory;

    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The storage format of the model's date columns.
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}

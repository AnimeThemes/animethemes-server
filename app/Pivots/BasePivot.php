<?php

declare(strict_types=1);

namespace App\Pivots;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class BasePivot.
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
abstract class BasePivot extends Pivot
{
    use HasFactory;

    public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}

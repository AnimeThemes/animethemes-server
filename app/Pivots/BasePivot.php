<?php

declare(strict_types=1);

namespace App\Pivots;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class BasePivot.
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
abstract class BasePivot extends Pivot
{
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}

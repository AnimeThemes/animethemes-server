<?php

declare(strict_types=1);

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class BasePivot
 * @package App\Pivots
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

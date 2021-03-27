<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

abstract class BasePivot extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}

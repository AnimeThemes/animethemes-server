<?php

declare(strict_types=1);

namespace App\Observers\Wiki;

use App\Models\Wiki\Performance;

class PerformanceObserver
{
    /**
     * Handle the Performance "creating" event.
     */
    public function creating(Performance $performance): void
    {
        $performance->role = 'Performance';
    }
}

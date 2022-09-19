<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Admin\Dump;

use App\Actions\Repositories\ReconcileResults;
use App\Models\Admin\Dump;

/**
 * Class ReconcileDumpResults.
 *
 * @extends ReconcileResults<Dump>
 */
class ReconcileDumpResults extends ReconcileResults
{
    /**
     * Get the model of the reconciliation results.
     *
     * @return class-string<Dump>
     */
    protected function model(): string
    {
        return Dump::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Wiki\Video;

use App\Actions\Repositories\ReconcileResults;
use App\Models\Wiki\Video;

/**
 * @extends ReconcileResults<Video>
 */
class ReconcileVideoResults extends ReconcileResults
{
    /**
     * Get the model of the reconciliation results.
     *
     * @return class-string<Video>
     */
    protected function model(): string
    {
        return Video::class;
    }
}

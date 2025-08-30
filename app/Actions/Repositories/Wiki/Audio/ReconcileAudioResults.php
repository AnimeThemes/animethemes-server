<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Wiki\Audio;

use App\Actions\Repositories\ReconcileResults;
use App\Models\Wiki\Audio;

/**
 * @extends ReconcileResults<Audio>
 */
class ReconcileAudioResults extends ReconcileResults
{
    /**
     * Get the model of the reconciliation results.
     *
     * @return class-string<Audio>
     */
    protected function model(): string
    {
        return Audio::class;
    }
}

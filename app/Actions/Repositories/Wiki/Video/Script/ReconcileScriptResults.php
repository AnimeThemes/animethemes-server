<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Wiki\Video\Script;

use App\Actions\Repositories\ReconcileResults;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class ReconcileScriptResults.
 *
 * @extends ReconcileResults<VideoScript>
 */
class ReconcileScriptResults extends ReconcileResults
{
    /**
     * Get the model of the reconciliation results.
     *
     * @return class-string<VideoScript>
     */
    protected function model(): string
    {
        return VideoScript::class;
    }
}

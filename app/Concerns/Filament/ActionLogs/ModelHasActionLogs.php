<?php

declare(strict_types=1);

namespace App\Concerns\Filament\ActionLogs;

use App\Models\Admin\ActionLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait ModelHasActionLogs.
 */
trait ModelHasActionLogs
{
    /**
     * Get the action logs for the model.
     *
     * @return MorphMany
     */
    public function actionlogs(): MorphMany
    {
        return $this->morphMany(ActionLog::class, 'actionable');
    }
}

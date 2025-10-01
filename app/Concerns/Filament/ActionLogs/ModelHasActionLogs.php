<?php

declare(strict_types=1);

namespace App\Concerns\Filament\ActionLogs;

use App\Models\Admin\ActionLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ModelHasActionLogs
{
    public function actionlogs(): MorphMany
    {
        return $this->morphMany(ActionLog::class, 'actionable');
    }
}

<?php

declare(strict_types=1);

namespace App\Observers\Admin;

use App\Enums\Models\Admin\ActionLogStatus;
use App\Models\Admin\ActionLog;
use Illuminate\Support\Facades\Session;

class ActionLogObserver
{
    /**
     * Handle the ActionLog "creating" event.
     */
    public function creating(ActionLog $actionLog): void
    {
        if ($actionLog->status === ActionLogStatus::RUNNING) {
            Session::put('currentActionLog', $actionLog->batch_id);
        }
    }

    /**
     * Handle the ActionLog "updating" event.
     */
    public function updating(ActionLog $actionLog): void
    {
        if ($actionLog->status === ActionLogStatus::RUNNING) {
            Session::put('currentActionLog', $actionLog->batch_id);
        }
    }
}

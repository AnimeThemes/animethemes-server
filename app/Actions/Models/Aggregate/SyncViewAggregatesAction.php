<?php

declare(strict_types=1);

namespace App\Actions\Models\Aggregate;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class SyncViewAggregatesAction.
 */
class SyncViewAggregatesAction
{
    /**
     * Handles the action.
     *
     * @return ActionResult
     *
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::statement('
                INSERT INTO view_aggregates (viewable_id, viewable_type, value)
                SELECT viewable_id, viewable_type, COUNT(*) as value
                FROM views
                GROUP BY viewable_type, viewable_id
                ON DUPLICATE KEY UPDATE value = VALUES(value);
            ');
        } catch (Exception $e) {
            return new ActionResult(
                ActionStatus::FAILED,
                $e->getMessage()
            );
        }

        return new ActionResult(
            ActionStatus::PASSED,
            'View aggregates synced successfully',
        );
    }
}

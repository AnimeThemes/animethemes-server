<?php

declare(strict_types=1);

namespace App\Actions\Models\Aggregate;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use Exception;
use Illuminate\Support\Facades\DB;

class SyncViewAggregatesAction
{
    /**
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::statement('
                CREATE TEMPORARY TABLE tmp_view_aggregates AS
                SELECT viewable_id, viewable_type, COUNT(*) as value
                FROM views
                GROUP BY viewable_type, viewable_id
            ');

            DB::statement('
                INSERT INTO view_aggregates (viewable_id, viewable_type, value)
                SELECT viewable_id, viewable_type, value
                FROM tmp_view_aggregates
                ON DUPLICATE KEY UPDATE value = VALUES(value)
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

<?php

declare(strict_types=1);

namespace App\Actions\Models\Aggregate;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class SyncLikeAggregatesAction.
 */
class SyncLikeAggregatesAction
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
            DB::statement("
                INSERT INTO like_aggregates (likeable_id, likeable_type, value)
                SELECT likeable_id, likeable_type, COUNT(*) as value
                FROM likes
                GROUP BY likeable_type, likeable_id
                ON DUPLICATE KEY UPDATE value = VALUES(value);
            ");

        } catch (Exception $e) {
            return new ActionResult(
                ActionStatus::FAILED,
                $e->getMessage()
            );
        }

        return new ActionResult(
            ActionStatus::PASSED,
            'Like aggregates synced successfully',
        );
    }
}

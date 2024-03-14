<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\External\Entry;

use App\Actions\Http\Api\ForceDeleteAction;
use App\Models\List\External\ExternalEntry;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ForceDeleteEntryAction.
 */
class ForceDeleteEntryAction
{
    /**
     * Force delete external entry.
     *
     * @param  ExternalEntry  $entry
     * @return string
     *
     * @throws Exception
     */
    public function forceDelete(ExternalEntry $entry): string
    {
        try {
            DB::beginTransaction();

            $forceDeleteAction = new ForceDeleteAction();

            $message = $forceDeleteAction->forceDelete($entry);

            DB::commit();

            return $message;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}

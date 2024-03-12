<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\External\Entry;

use App\Actions\Http\Api\DestroyAction;
use App\Models\List\External\ExternalEntry;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class DestroyEntryAction.
 */
class DestroyEntryAction
{
    /**
     * Destroy external entry.
     *
     * @param  ExternalEntry  $entry
     * @return Model
     *
     * @throws Exception
     */
    public function destroy(ExternalEntry $entry): Model
    {
        try {
            DB::beginTransaction();

            $entry->external_profile()->disassociate()->save();

            $destroyAction = new DestroyAction();

            $destroyed = $destroyAction->destroy($entry);

            DB::commit();

            return $destroyed;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
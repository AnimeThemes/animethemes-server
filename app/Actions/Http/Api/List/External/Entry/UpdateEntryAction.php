<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\External\Entry;

use App\Actions\Http\Api\UpdateAction;
use App\Models\List\External\ExternalEntry;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class UpdateEntryAction.
 */
class UpdateEntryAction
{
    /**
     * Update external entry.
     *
     * @param  ExternalEntry  $entry
     * @param  array  $parameters
     * @return Model
     *
     * @throws Exception
     */
    public function update(ExternalEntry $entry, array $parameters): Model
    {
        try {
            DB::beginTransaction();

            $updateAction = new UpdateAction();

            $updateAction->update($entry, $parameters);

            DB::commit();

            return $updateAction->cleanup($entry);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
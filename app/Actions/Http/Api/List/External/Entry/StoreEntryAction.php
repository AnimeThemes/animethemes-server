<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\External\Entry;

use App\Actions\Http\Api\StoreAction;
use App\Models\List\ExternalProfile;
use App\Models\List\External\ExternalEntry;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreEntryAction.
 */
class StoreEntryAction
{
    /**
     * Store playlist track.
     *
     * @param  ExternalProfile  $profile
     * @param  Builder  $builder
     * @param  array  $parameters
     * @return Model
     *
     * @throws Exception
     */
    public function store(ExternalProfile $profile, Builder $builder, array $parameters): Model
    {
        try {
            DB::beginTransaction();

            $storeAction = new StoreAction();

            /** @var ExternalEntry */
            $entry = $storeAction->store($builder, $parameters);

            $profile->external_entries()->save($entry);

            DB::commit();

            return $storeAction->cleanup($entry);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}

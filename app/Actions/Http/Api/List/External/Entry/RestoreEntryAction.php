<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\External\Entry;

use App\Actions\Http\Api\RestoreAction;
use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class RestoreEntryAction.
 */
class RestoreEntryAction
{
    /**
     * Restore external entry.
     *
     * @param  ExternalProfile  $profile
     * @param  ExternalEntry  $entry
     * @return Model
     *
     * @throws Exception
     */
    public function restore(ExternalProfile $profile, ExternalEntry $entry): Model
    {
        try {
            DB::beginTransaction();

            $restoreAction = new RestoreAction();

            $restoreAction->restore($entry);

            $entry->externalprofile()->associate($profile)->save();

            $restored = $restoreAction->cleanup($entry);

            DB::commit();

            return $restored;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
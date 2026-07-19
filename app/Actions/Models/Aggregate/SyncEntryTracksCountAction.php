<?php

declare(strict_types=1);

namespace App\Actions\Models\Aggregate;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Exception;
use Illuminate\Support\Facades\DB;

class SyncEntryTracksCountAction
{
    /**
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        $entryModel = new AnimeThemeEntry();

        try {
            $tracksCount = PlaylistTrack::query()
                ->select(PlaylistTrack::ATTRIBUTE_ENTRY)
                ->selectRaw('COUNT(*) AS total')
                ->groupBy(PlaylistTrack::ATTRIBUTE_ENTRY);

            AnimeThemeEntry::query()
                ->leftJoinSub(
                    $tracksCount,
                    'track_counts',
                    'track_counts.entry_id',
                    '=',
                    $entryModel->getQualifiedKeyName(),
                )
                ->toBase()
                ->update([
                    $entryModel->qualifyColumn('tracks_count') => DB::raw('COALESCE(track_counts.total, 0)'),
                ]);
        } catch (Exception $e) {
            return new ActionResult(ActionStatus::FAILED, $e->getMessage());
        }

        return new ActionResult(ActionStatus::PASSED, 'Tracks count synced successfully');
    }
}

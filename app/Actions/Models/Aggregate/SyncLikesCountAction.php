<?php

declare(strict_types=1);

namespace App\Actions\Models\Aggregate;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use App\Models\User\Like;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SyncLikesCountAction
{
    /**
     * @param  class-string<Model>  $modelClass
     *
     * @throws Exception
     */
    public function handle(string $modelClass): ActionResult
    {
        $model = new $modelClass;

        try {
            $likeCounts = Like::query()
                ->select(Like::ATTRIBUTE_LIKEABLE_ID)
                ->selectRaw('COUNT(*) AS total')
                ->where(Like::ATTRIBUTE_LIKEABLE_TYPE, $model->getMorphClass())
                ->groupBy(Like::ATTRIBUTE_LIKEABLE_ID);

            AnimeThemeEntry::query()
                ->leftJoinSub(
                    $likeCounts,
                    'like_counts',
                    'like_counts.likeable_id',
                    '=',
                    $model->getQualifiedKeyName(),
                )
                ->toBase()
                ->update([
                    $model->qualifyColumn('likes_count') => DB::raw('COALESCE(like_counts.total, 0)'),
                ]);
        } catch (Exception $e) {
            return new ActionResult(ActionStatus::FAILED, $e->getMessage());
        }

        return new ActionResult(ActionStatus::PASSED, 'Likes count synced successfully');
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Http\Api\StoreAction;
use App\Enums\Models\List\AnimeWatchStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreExternalProfileAction.
 */
class StoreExternalProfileAction
{
    /**
     * Store external profile and its entries.
     *
     * @param  Builder  $builder
     * @param  array  $profileParameters
     * @return Model
     * 
     * @throws Exception
     */
    public function store(Builder $builder, array $profileParameters): Model
    {
        try {
            DB::beginTransaction();

            // $getEntriesAction = new GetEntries();

            // $entries = $getEntriesAction->get($profileParameters);

            $entries = [
                'name' => 'Kyrch Profile',
                'entries' => [
                    [
                        ExternalResource::ATTRIBUTE_EXTERNAL_ID => 101573,
                        ExternalEntry::ATTRIBUTE_SCORE => 10,
                        ExternalEntry::ATTRIBUTE_IS_FAVOURITE => true,
                        ExternalEntry::ATTRIBUTE_WATCH_STATUS => AnimeWatchStatus::COMPLETED->value,
                    ],
                    [
                        ExternalResource::ATTRIBUTE_EXTERNAL_ID => 477,
                        ExternalEntry::ATTRIBUTE_SCORE => 9.5,
                        ExternalEntry::ATTRIBUTE_IS_FAVOURITE => false,
                        ExternalEntry::ATTRIBUTE_WATCH_STATUS => AnimeWatchStatus::WATCHING->value,
                    ],
                    [
                        ExternalResource::ATTRIBUTE_EXTERNAL_ID => 934,
                        ExternalEntry::ATTRIBUTE_SCORE => 8,
                        ExternalEntry::ATTRIBUTE_IS_FAVOURITE => false,
                        ExternalEntry::ATTRIBUTE_WATCH_STATUS => AnimeWatchStatus::PAUSED->value,
                    ],
                ],
            ];

            $storeProfileAction = new StoreAction();

            $profile = $storeProfileAction->store($builder, $profileParameters);

            foreach (Arr::get($entries, 'entries') as $entryParameters) {
                $storeEntryAction = new StoreAction();

                $externalSite = Arr::get($profileParameters, 'site');
                $external_id = Arr::get($entryParameters, 'external_id');

                $animes = Anime::query()->whereHas(ExternalResource::TABLE, function (Builder $query) use ($externalSite, $external_id) {
                    $query->where(ExternalResource::ATTRIBUTE_SITE, ExternalProfileSite::getResourceSite($externalSite)->value)
                        ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $external_id);
                })->get();
                
                foreach ($animes as $anime) {
                    if ($anime instanceof Anime) {
                        $storeEntryAction->store(ExternalEntry::query(), array_merge(
                            $entryParameters,
                            [
                                ExternalEntry::ATTRIBUTE_ANIME => $anime->getKey(),
                                ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE => $profile->getKey(),
                            ]
                        ));
                    }
                }
            }

            DB::commit();

            return $storeProfileAction->cleanup($profile);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}

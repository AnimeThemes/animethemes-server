<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class SyncExternalProfileAction.
 */
class SyncExternalProfileAction
{
    protected Collection $resources;

    /**
     * Sync the profile.
     *
     * @param  ExternalProfile  $profile
     * @return ExternalProfile
     *
     * @throws Exception
     */
    public function handle(ExternalProfile $profile): ExternalProfile
    {
        try {
            DB::beginTransaction();

            $action = $profile->isClaimed()
                ? $this->getClaimedActionClass($profile)
                : $this->getUnclaimedActionClass($profile);

            $entries = $action->getEntries();

            $this->preloadResources($profile->site, $entries);

            $externalEntries = [];
            foreach ($entries as $entry) {
                $externalId = Arr::get($entry, 'external_id');

                foreach ($this->getAnimesByExternalId($externalId) as $anime) {
                    $externalEntries[] = [
                        ExternalEntry::ATTRIBUTE_SCORE => Arr::get($entry, ExternalEntry::ATTRIBUTE_SCORE),
                        ExternalEntry::ATTRIBUTE_IS_FAVORITE => Arr::get($entry, ExternalEntry::ATTRIBUTE_IS_FAVORITE),
                        ExternalEntry::ATTRIBUTE_WATCH_STATUS => Arr::get($entry, ExternalEntry::ATTRIBUTE_WATCH_STATUS),
                        ExternalEntry::ATTRIBUTE_ANIME => $anime->getKey(),
                        ExternalEntry::ATTRIBUTE_PROFILE => $profile->getKey(),
                    ];
                }
            }

            ExternalEntry::upsert($externalEntries, [ExternalEntry::ATTRIBUTE_ANIME, ExternalEntry::ATTRIBUTE_PROFILE]);

            $profile->update([ExternalProfile::ATTRIBUTE_SYNCED_AT => now()]);

            DB::commit();

            return $profile;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Get the mapping for the entries token class.
     *
     * @param  ExternalProfile  $profile
     * @return BaseExternalEntryTokenAction
     *
     * @throws RuntimeException
     */
    protected function getClaimedActionClass(ExternalProfile $profile): BaseExternalEntryTokenAction
    {
        return StoreExternalProfileTokenAction::getActionClass($profile->site, $profile->externaltoken);
    }

    /**
     * Get the mapping for the entries class.
     *
     * @param  ExternalProfile  $profile
     * @return BaseExternalEntryAction
     *
     * @throws RuntimeException
     */
    protected function getUnclaimedActionClass(ExternalProfile $profile): BaseExternalEntryAction
    {
        return StoreExternalProfileUsernameAction::getActionClass($profile->site, $profile);
    }

    /**
     * Preload the resources for performance proposals.
     *
     * @param  ExternalProfileSite  $profileSite
     * @param  array  $entries
     * @return void
     */
    protected function preloadResources(ExternalProfileSite $profileSite, array $entries): void
    {
        $this->resources = Cache::flexible("externalprofile_resources", [60, 300], function () use ($profileSite, $entries) {
            return ExternalResource::query()
                ->where(ExternalResource::ATTRIBUTE_SITE, $profileSite->getResourceSite()->value)
                ->whereIn(ExternalResource::ATTRIBUTE_EXTERNAL_ID, Arr::pluck($entries, 'external_id'))
                ->with(ExternalResource::RELATION_ANIME)
                ->get()
                ->mapWithKeys(fn (ExternalResource $resource) => [$resource->external_id => $resource->anime]);
        });
    }

    /**
     * Get the animes by the external id.
     *
     * @param  int  $externalId
     * @return Collection<int, Anime>
     */
    protected function getAnimesByExternalId(int $externalId): Collection
    {
        return $this->resources[$externalId] ?? new Collection();
    }
}

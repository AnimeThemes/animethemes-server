<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Events\List\ExternalProfile\ExternalProfileSynced;
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

            $this->cacheResources($profile->site);

            $externalEntries = [];
            foreach ($entries as $entry) {
                $externalId = Arr::get($entry, 'external_id');

                foreach ($this->getAnimesByExternalId($externalId) as $anime) {
                    $externalEntries[] = [
                        ExternalEntry::ATTRIBUTE_SCORE => Arr::get($entry, ExternalEntry::ATTRIBUTE_SCORE),
                        ExternalEntry::ATTRIBUTE_IS_FAVORITE => Arr::get($entry, ExternalEntry::ATTRIBUTE_IS_FAVORITE),
                        ExternalEntry::ATTRIBUTE_WATCH_STATUS => Arr::get($entry, ExternalEntry::ATTRIBUTE_WATCH_STATUS),
                        ExternalEntry::ATTRIBUTE_ANIME => $anime,
                        ExternalEntry::ATTRIBUTE_PROFILE => $profile->getKey(),
                    ];
                }
            }

            ExternalEntry::upsert($externalEntries, [ExternalEntry::ATTRIBUTE_ANIME, ExternalEntry::ATTRIBUTE_PROFILE]);

            $profile->update([ExternalProfile::ATTRIBUTE_SYNCED_AT => now()]);

            DB::commit();

            ExternalProfileSynced::dispatch($profile);

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
     * Cache the resources for performance proposals.
     *
     * @param  ExternalProfileSite  $profileSite
     * @return void
     */
    protected function cacheResources(ExternalProfileSite $profileSite): void
    {
        $this->resources = Cache::flexible("resources_{$profileSite->getResourceSite()->localize()}", [60, 300], function () use ($profileSite) {
            return ExternalResource::query()
                ->where(ExternalResource::ATTRIBUTE_SITE, $profileSite->getResourceSite()->value)
                ->with([ExternalResource::RELATION_ANIME => fn ($query) => $query->select([Anime::TABLE.'.'.Anime::ATTRIBUTE_ID])])
                ->whereHas(ExternalResource::RELATION_ANIME)
                ->get()
                ->mapWithKeys(fn (ExternalResource $resource) => [$resource->external_id => $resource->anime->map(fn (Anime $anime) => $anime->getKey())]);
        });
    }

    /**
     * Get the animes by the external id.
     *
     * @param  int  $externalId
     * @return Collection<int, int>
     */
    protected function getAnimesByExternalId(int $externalId): Collection
    {
        return $this->resources->get($externalId) ?? collect();
    }
}

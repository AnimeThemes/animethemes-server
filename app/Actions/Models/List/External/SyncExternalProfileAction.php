<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External;

use App\Actions\Models\List\External\Entry\BaseExternalEntryClaimedAction;
use App\Actions\Models\List\External\Entry\BaseExternalEntryUnclaimedAction;
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

class SyncExternalProfileAction
{
    protected Collection $resources;

    /**
     * @throws Exception
     */
    public function handle(ExternalProfile $profile): ExternalProfile
    {
        try {
            DB::beginTransaction();

            // A claimed profile will be synced using its external token.
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

            // Insert the external entries or update its status filtering by anime id.
            ExternalEntry::query()
                ->upsert(
                    $externalEntries,
                    [ExternalEntry::ATTRIBUTE_ANIME, ExternalEntry::ATTRIBUTE_PROFILE],
                    ExternalEntry::fieldsForUpdate(),
                );

            // An external entry that is not in the external list anymore should be removed.
            ExternalEntry::query()
                ->where(ExternalEntry::ATTRIBUTE_PROFILE, $profile->getKey())
                ->whereNotIn(ExternalEntry::ATTRIBUTE_ANIME, Arr::map($externalEntries, fn ($value) => $value[ExternalEntry::ATTRIBUTE_ANIME]))
                ->delete();

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
     */
    protected function getClaimedActionClass(ExternalProfile $profile): BaseExternalEntryClaimedAction
    {
        return StoreExternalProfileClaimedAction::getActionClass($profile->site, $profile->externaltoken);
    }

    /**
     * Get the mapping for the entries class.
     */
    protected function getUnclaimedActionClass(ExternalProfile $profile): BaseExternalEntryUnclaimedAction
    {
        return StoreExternalProfileUnclaimedAction::getActionClass($profile->site, $profile);
    }

    /**
     * Cache the resources for performance proposals.
     */
    protected function cacheResources(ExternalProfileSite $profileSite): void
    {
        // External resources are only added by mods so it doesn't change too often.
        $this->resources = Cache::flexible("resources_{$profileSite->name}", [60, 300], function () use ($profileSite) {
            return ExternalResource::query()
                ->where(ExternalResource::ATTRIBUTE_SITE, $profileSite->getResourceSite()->value)
                ->with([ExternalResource::RELATION_ANIME => fn ($query) => $query->select([Anime::TABLE.'.'.Anime::ATTRIBUTE_ID])])
                ->whereHas(ExternalResource::RELATION_ANIME)
                ->get()
                ->mapWithKeys(fn (ExternalResource $resource) => [$resource->external_id => $resource->anime->map(fn (Anime $anime) => $anime->getKey())]);
        });
    }

    /**
     * @return Collection<int, int>
     */
    protected function getAnimesByExternalId(int $externalId): Collection
    {
        return $this->resources->get($externalId) ?? collect();
    }
}

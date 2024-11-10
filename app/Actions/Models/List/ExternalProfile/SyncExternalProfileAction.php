<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Models\List\BaseStoreExternalProfileAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Site\AnilistExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Site\AnilistExternalEntryTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncExternalProfileAction.
 */
class SyncExternalProfileAction extends BaseStoreExternalProfileAction
{
    /**
     * Sync the profile.
     *
     * @param  ExternalProfile  $profile
     * @return ExternalProfile|null
     */
    public function handle(ExternalProfile $profile): ?ExternalProfile
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

            //ExternalEntry::insert($externalEntries);

            $profile->externalentries()->upsert($externalEntries, [ExternalEntry::ATTRIBUTE_ANIME, ExternalEntry::ATTRIBUTE_PROFILE]);

            // Delete the old entries before creating new ones.
            //ExternalEntry::withoutEvents(fn () => ExternalEntry::query()->whereBelongsTo($profile)->delete());


            return $profile;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            return null;
        }
    }

    /**
     * Get the mapping for the entries token class.
     *
     * @param  ExternalProfile  $profile
     * @return BaseExternalEntryTokenAction|null
     */
    protected function getClaimedActionClass(ExternalProfile $profile): ?BaseExternalEntryTokenAction
    {
        return match ($profile->site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryTokenAction($profile->externaltoken),
            default => null,
        };
    }

    /**
     * Get the mapping for the entries class.
     *
     * @param  ExternalProfile  $profile
     * @return BaseExternalEntryAction|null
     */
    protected function getUnclaimedActionClass(ExternalProfile $profile): ?BaseExternalEntryAction
    {
        return match ($profile->site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryAction($profile->toArray()),
            default => null,
        };
    }
}

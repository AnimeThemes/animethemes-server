<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\BaseStoreExternalProfileAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Site\AnilistExternalEntryAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreExternalProfileUsernameAction.
 */
class StoreExternalProfileUsernameAction extends BaseStoreExternalProfileAction
{
    /**
     * Find or store an external profile and its entries given determined username.
     *
     * @param  Builder  $builder
     * @param  array  $profileParameters
     * @return ExternalProfile|null
     *
     * @throws Exception
     */
    public function findOrCreate(Builder $builder, array $profileParameters): ?ExternalProfile
    {
        try {
            $profileSite = ExternalProfileSite::fromLocalizedName(Arr::get($profileParameters, 'site'));

            $findProfile = ExternalProfile::query()
                ->where(ExternalProfile::ATTRIBUTE_NAME, Arr::get($profileParameters, 'name'))
                ->where(ExternalProfile::ATTRIBUTE_SITE, $profileSite->value)
                ->first();

            if ($findProfile instanceof ExternalProfile) {
                return $findProfile;
            }

            DB::beginTransaction();

            $action = $this->getActionClass($profileSite, $profileParameters);

            if ($action === null) {
                return null;
            }

            $entries = $action->getEntries();

            $this->preloadResources($profileSite, $entries);

            $storeAction = new StoreAction();

            /** @var ExternalProfile $profile */
            $profile = $storeAction->store($builder, [
                ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID => $action->getId(),
                ExternalProfile::ATTRIBUTE_NAME => Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_NAME),
                ExternalProfile::ATTRIBUTE_SITE => $profileSite->value,
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::fromLocalizedName(Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_VISIBILITY))->value,
            ]);

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

            ExternalEntry::insert($externalEntries);

            DB::commit();

            return $profile;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            return null;
        }
    }

    /**
     * Get the mapping for the entries class.
     *
     * @param  ExternalProfileSite  $site
     * @param  array  $profileParameters
     * @return BaseExternalEntryAction|null
     */
    protected function getActionClass(ExternalProfileSite $site, array $profileParameters): ?BaseExternalEntryAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryAction($profileParameters),
            default => null,
        };
    }
}
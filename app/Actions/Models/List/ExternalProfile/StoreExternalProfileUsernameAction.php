<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Username\AnilistExternalEntryAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class StoreExternalProfileUsernameAction.
 */
class StoreExternalProfileUsernameAction
{
    /**
     * Get the first record or store an external profile and its entries given determined username.
     *
     * @param  Builder  $builder
     * @param  array  $profileParameters
     * @return ExternalProfile
     *
     * @throws Exception
     */
    public function firstOrCreate(Builder $builder, array $profileParameters): ExternalProfile
    {
        $name = Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_NAME);
        $siteLocalized = Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_SITE);
        $visibilityLocalized = Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_VISIBILITY);

        try {
            DB::beginTransaction();

            $profileSite = ExternalProfileSite::fromLocalizedName($siteLocalized);

            $findProfile = ExternalProfile::query()
                ->where(ExternalProfile::ATTRIBUTE_NAME, $name)
                ->where(ExternalProfile::ATTRIBUTE_SITE, $profileSite->value)
                ->first();

            if ($findProfile instanceof ExternalProfile) {
                DB::rollBack();
                return $findProfile;
            }

            $action = static::getActionClass($profileSite, $profileParameters);

            $storeAction = new StoreAction();

            /** @var ExternalProfile $profile */
            $profile = $storeAction->store($builder, [
                ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID => $action->getId(),
                ExternalProfile::ATTRIBUTE_NAME => $name,
                ExternalProfile::ATTRIBUTE_SITE => $profileSite->value,
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::fromLocalizedName($visibilityLocalized)->value,
            ]);

            DB::commit();

            return $profile;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Get the mapping for the entries class.
     *
     * @param  ExternalProfileSite  $site
     * @param  ExternalProfile|array  $profile
     * @return BaseExternalEntryAction
     *
     * @throws RuntimeException
     */
    public static function getActionClass(ExternalProfileSite $site, ExternalProfile|array $profile): BaseExternalEntryAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryAction($profile),
            default => throw new RuntimeException("External entry action not configured for site {$site->localize()}"),
        };
    }
}
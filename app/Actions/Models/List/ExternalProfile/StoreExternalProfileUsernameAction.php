<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Username\AnilistExternalEntryAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
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
class StoreExternalProfileUsernameAction
{
    /**
     * Find or store an external profile and its entries given determined username.
     *
     * @param  Builder  $builder
     * @param  array  $profileParameters
     * @return ExternalProfile
     *
     * @throws Exception
     */
    public function findOrCreate(Builder $builder, array $profileParameters): ExternalProfile
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
                throw new Error("Action not found for site {$profileSite->localize()}", 404);
            }

            $storeAction = new StoreAction();

            /** @var ExternalProfile $profile */
            $profile = $storeAction->store($builder, [
                ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID => $action->getId(),
                ExternalProfile::ATTRIBUTE_NAME => Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_NAME),
                ExternalProfile::ATTRIBUTE_SITE => $profileSite->value,
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::fromLocalizedName(Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_VISIBILITY))->value,
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
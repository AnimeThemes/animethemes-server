<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\External\Entry\BaseExternalEntryUnclaimedAction;
use App\Actions\Models\List\External\Entry\Unclaimed\AnilistExternalEntryUnclaimedAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class StoreExternalProfileUnclaimedAction
{
    /**
     * Get the first record or store an external profile and its entries given determined username.
     *
     * @throws Exception
     */
    public function firstOrCreate(Builder $builder, array $profileParameters): ExternalProfile
    {
        $name = Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_NAME);
        $site = ExternalProfileSite::fromLocalizedName(Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_SITE));
        $visibility = ExternalProfileVisibility::fromLocalizedName(Arr::get($profileParameters, ExternalProfile::ATTRIBUTE_VISIBILITY));

        try {
            DB::beginTransaction();

            $findProfile = ExternalProfile::query()
                ->where(ExternalProfile::ATTRIBUTE_NAME, $name)
                ->where(ExternalProfile::ATTRIBUTE_SITE, $site->value)
                ->first();

            if ($findProfile instanceof ExternalProfile) {
                throw_if($findProfile->isClaimed(), AuthorizationException::class, "The external profile '{$findProfile->getName()}' is already claimed.");

                DB::rollBack();

                return $findProfile;
            }

            $action = static::getActionClass($site, $profileParameters);

            /** @var StoreAction<ExternalProfile> $storeAction */
            $storeAction = new StoreAction();

            $profile = $storeAction->store($builder, [
                ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID => $action->getUserId(),
                ExternalProfile::ATTRIBUTE_NAME => $name,
                ExternalProfile::ATTRIBUTE_SITE => $site->value,
                ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->value,
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
     *
     * @throws RuntimeException
     */
    public static function getActionClass(ExternalProfileSite $site, ExternalProfile|array $profile): BaseExternalEntryUnclaimedAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryUnclaimedAction($profile),
            default => throw new RuntimeException("External entry action not configured for site {$site->localize()}"),
        };
    }
}

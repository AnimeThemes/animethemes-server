<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Token\AnilistExternalEntryTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Token\MalExternalEntryTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class StoreExternalProfileTokenAction.
 */
class StoreExternalProfileTokenAction
{
    protected Collection $resources;

    /**
     * Get the first record or store external profile given determined external token.
     *
     * @param  ExternalToken  $token
     * @param  array  $parameters
     * @return ExternalProfile|null
     *
     * @throws Exception
     */
    public function firstOrCreate(ExternalToken $token, array $parameters): ?ExternalProfile
    {
        try {
            $site = ExternalProfileSite::fromLocalizedName(Arr::get($parameters, 'site'));

            $action = static::getActionClass($site, $token);

            $userId = $action->getUserId();

            $profile = $this->firstForUserIdOrCreate($userId, $site, $action, $parameters);

            return $profile;

        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }

    /**
     * Get the first record or create the profile for a userId and site.
     *
     * @param  int  $userId
     * @param  ExternalProfileSite  $site
     * @param  BaseExternalEntryTokenAction  $action
     * @param  array  $parameters
     * @return ExternalProfile
     */
    protected function firstForUserIdOrCreate(int $userId, ExternalProfileSite $site, BaseExternalEntryTokenAction $action, array $parameters): ExternalProfile
    {
        $claimedProfile = ExternalProfile::query()
            ->where(ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID, $userId)
            ->where(ExternalProfile::ATTRIBUTE_SITE, $site->value)
            ->whereHas(ExternalProfile::RELATION_USER)
            ->first();

        if ($claimedProfile instanceof ExternalProfile) {
            return $claimedProfile;
        }

        $unclaimedProfile = ExternalProfile::query()
            ->where(ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID, $userId)
            ->where(ExternalProfile::ATTRIBUTE_SITE, $site->value)
            ->whereDoesntHave(ExternalProfile::RELATION_USER)
            ->first();

        if ($unclaimedProfile instanceof ExternalProfile) {
            $unclaimedProfile->update([
                ExternalProfile::ATTRIBUTE_USER => Arr::get($parameters, ExternalProfile::ATTRIBUTE_USER),
                ExternalProfile::ATTRIBUTE_NAME => $action->getUsername(),
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

            return $unclaimedProfile;
        }

        $storeAction = new StoreAction();

        /** @var ExternalProfile $profile */
        $profile = $storeAction->store(ExternalProfile::query(), [
            ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID => $userId,
            ExternalProfile::ATTRIBUTE_USER => Arr::get($parameters, ExternalProfile::ATTRIBUTE_USER),
            ExternalProfile::ATTRIBUTE_NAME => $action->getUsername(),
            ExternalProfile::ATTRIBUTE_SITE => $site->value,
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

        return $profile;
    }

    /**
     * Get the mapping for the entries token class.
     *
     * @param  ExternalProfileSite  $site
     * @param  ExternalToken  $token
     * @return BaseExternalEntryTokenAction
     *
     * @throws RuntimeException
     */
    public static function getActionClass(ExternalProfileSite $site, ExternalToken $token): BaseExternalEntryTokenAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryTokenAction($token),
            ExternalProfileSite::MAL => new MalExternalEntryTokenAction($token),
            default => throw new RuntimeException("External entry token action not configured for site {$site->localize()}"),
        };
    }
}

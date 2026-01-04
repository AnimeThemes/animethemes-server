<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\External\Entry\BaseExternalEntryClaimedAction;
use App\Actions\Models\List\External\Entry\Claimed\AnilistExternalEntryClaimedAction;
use App\Actions\Models\List\External\Entry\Claimed\MalExternalEntryClaimedAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class StoreExternalProfileClaimedAction
{
    protected Collection $resources;

    /**
     * Get the first record or store external profile given determined external token.
     *
     *
     * @throws Exception
     */
    public function firstOrCreate(ExternalToken $token, array $parameters): ExternalProfile
    {
        try {
            $site = ExternalProfileSite::fromLocalizedName(Arr::get($parameters, ExternalProfile::ATTRIBUTE_SITE));

            $action = static::getActionClass($site, $token);

            $userId = $action->getUserId();

            return $this->firstForUserIdOrCreate($userId, $site, $action, $parameters);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }

    /**
     * Get the first record or create the profile for a userId and site.
     */
    protected function firstForUserIdOrCreate(int $userId, ExternalProfileSite $site, BaseExternalEntryClaimedAction $action, array $parameters): ExternalProfile
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
            return tap(
                $unclaimedProfile,
                fn ($unclaimedProfile) => $unclaimedProfile->update([
                    ExternalProfile::ATTRIBUTE_USER => Arr::integer($parameters, ExternalProfile::ATTRIBUTE_USER),
                    ExternalProfile::ATTRIBUTE_NAME => $action->getUsername(),
                    ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
                ])
            );
        }

        /** @var StoreAction<ExternalProfile> $storeAction */
        $storeAction = new StoreAction();

        return $storeAction->store(ExternalProfile::query(), [
            ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID => $userId,
            ExternalProfile::ATTRIBUTE_USER => Arr::integer($parameters, ExternalProfile::ATTRIBUTE_USER),
            ExternalProfile::ATTRIBUTE_NAME => $action->getUsername(),
            ExternalProfile::ATTRIBUTE_SITE => $site->value,
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);
    }

    /**
     * Get the mapping for the entries token class.
     *
     * @throws RuntimeException
     */
    public static function getActionClass(ExternalProfileSite $site, ExternalToken $token): BaseExternalEntryClaimedAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryClaimedAction($token),
            ExternalProfileSite::MAL => new MalExternalEntryClaimedAction($token),
            default => throw new RuntimeException("External entry token action not configured for site {$site->localize()}"),
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Models\List;

use App\Actions\Models\List\External\StoreExternalProfileClaimedAction;
use App\Actions\Models\List\External\Token\BaseExternalTokenAction;
use App\Actions\Models\List\External\Token\Site\AnilistExternalTokenAction;
use App\Actions\Models\List\External\Token\Site\MalExternalTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ExternalTokenCallbackAction
{
    /**
     * We should store the token and the profile.
     *
     * @param  array  $parameters
     *
     * @throws Exception
     */
    public function store(array $parameters): ExternalProfile
    {
        try {
            DB::beginTransaction();

            $site = Arr::get($parameters, ExternalProfile::ATTRIBUTE_SITE);
            $profileSite = ExternalProfileSite::fromLocalizedName($site);

            $externalToken = ExternalToken::query()
                ->whereRelation(ExternalToken::RELATION_PROFILE, ExternalProfile::ATTRIBUTE_USER, Auth::id())
                ->whereRelation(ExternalToken::RELATION_PROFILE, ExternalProfile::ATTRIBUTE_SITE, $profileSite->value)
                ->first();

            if (! $externalToken instanceof ExternalToken) {
                $externalToken = $this->getActionClass($profileSite)->store($parameters);
            }

            $profileAction = new StoreExternalProfileClaimedAction();

            $profile = $profileAction->firstOrCreate($externalToken, $parameters);

            $profile->externaltoken()->save($externalToken);

            DB::commit();

            return $profile;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Get the mapping for the token class.
     *
     * @throws RuntimeException
     */
    protected function getActionClass(ExternalProfileSite $site): BaseExternalTokenAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalTokenAction(),
            ExternalProfileSite::MAL => new MalExternalTokenAction(),
            default => throw new RuntimeException("External token action not configured for site {$site->localize()}"),
        };
    }
}

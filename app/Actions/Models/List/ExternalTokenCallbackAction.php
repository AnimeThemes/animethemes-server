<?php

declare(strict_types=1);

namespace App\Actions\Models\List;

use App\Actions\Models\List\ExternalProfile\ExternalToken\BaseExternalTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalToken\Site\AnilistExternalTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalToken\Site\MalExternalTokenAction;
use App\Actions\Models\List\ExternalProfile\StoreExternalProfileTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\ExternalProfile;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class ExternalTokenCallbackAction.
 */
class ExternalTokenCallbackAction
{
    /**
     * We should store the token and the profile.
     *
     * @param  array  $parameters
     * @return ExternalProfile
     *
     * @throws Exception
     */
    public function store(array $parameters): ExternalProfile
    {
        try {
            DB::beginTransaction();

            $site = Arr::get($parameters, 'site');
            $profileSite = ExternalProfileSite::fromLocalizedName($site);

            $action = $this->getActionClass($profileSite);

            $externalToken = $action->store($parameters);

            $profileAction = new StoreExternalProfileTokenAction();

            $profile = $profileAction->firstOrCreate($externalToken, $parameters);

            $externalToken->externalprofile()->associate($profile);

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
     * @param  ExternalProfileSite  $site
     * @return BaseExternalTokenAction
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

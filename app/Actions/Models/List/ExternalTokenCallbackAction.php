<?php

declare(strict_types=1);

namespace App\Actions\Models\List;

use App\Actions\Models\List\ExternalProfile\ExternalToken\BaseExternalTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalToken\Site\AnilistExternalTokenAction;
use App\Actions\Models\List\ExternalProfile\StoreExternalProfileTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\ExternalProfile;
use Error;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            if ($action === null) {
                throw new Error("Action not found for site {$profileSite->localize()}", 404);
            }

            $externalToken = $action->store(Arr::get($parameters, 'code'));

            if ($externalToken === null) {
                throw new Error('Invalid Code', 400);
            }

            $profileAction = new StoreExternalProfileTokenAction();

            $profile = $profileAction->findOrCreate($externalToken, $parameters);

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
     * @return BaseExternalTokenAction|null
     */
    protected function getActionClass(ExternalProfileSite $site): ?BaseExternalTokenAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalTokenAction(),
            default => null,
        };
    }
}

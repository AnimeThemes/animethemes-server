<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Site\AnilistExternalEntryAction;
use App\Actions\Models\List\ExternalProfile\ExternalEntry\Site\AnilistExternalEntryTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\External\ExternalEntry;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreExternalProfileAction.
 */
class StoreExternalProfileAction
{
    protected Collection $resources;

    /**
     * Find or store external profile and its entries given determined external token.
     *
     * @param  ExternalToken  $token
     * @param  array  $parameters
     * @return ExternalProfile
     *
     * @throws Exception
     */
    public function findOrCreateForExternalToken(ExternalToken $token, array $parameters): ExternalProfile
    {
        try {
            DB::beginTransaction();

            $storeAction = new StoreAction();

            $site = ExternalProfileSite::fromLocalizedName(Arr::get($parameters, 'site'));

            $action = $this->getActionClassToken($site);

            if ($action === null) {
                throw new Error("Undefined action for site {$site->localize()}"); // TODO: check if it is working
            }

            $entries = $action->getEntries();

            $this->preloadResources($site, $entries);

            $profile = $storeAction->store(ExternalProfile::query(), [
                ExternalProfile::ATTRIBUTE_USER => Arr::get($parameters, ExternalProfile::ATTRIBUTE_USER),
                ExternalProfile::ATTRIBUTE_NAME => '', // TODO
                ExternalProfile::ATTRIBUTE_SITE => $site->value,
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

            $token->externalprofile()->associate($profile);

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

            throw $e;
        }
    }

    /**
     * Find or store an external profile and its entries given determined username.
     *
     * @param  Builder  $builder
     * @param  array  $profileParameters
     * @return ExternalProfile
     *
     * @throws Exception
     */
    public function findOrCreateForUsername(Builder $builder, array $profileParameters): ExternalProfile
    {
        try {
            DB::beginTransaction();

            $storeAction = new StoreAction();

            $profileSite = ExternalProfileSite::fromLocalizedName(Arr::get($profileParameters, 'site'));

            $action = $this->getActionClass($profileSite, $profileParameters);

            if ($action === null) {
                throw new Error("Undefined action for site {$profileSite->localize()}"); // TODO: check if it is working
            }

            $entries = $action->getEntries();

            $this->preloadResources($profileSite, $entries);

            $profile = $storeAction->store($builder, [
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

            throw $e;
        }
    }

    /**
     * Get the mapping for the entries token class.
     *
     * @param  ExternalProfileSite  $site
     * @return BaseExternalEntryTokenAction|null
     */
    protected function getActionClassToken(ExternalProfileSite $site): ?BaseExternalEntryTokenAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalEntryTokenAction(),
            default => null,
        };
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

    /**
     * Preload the resources for performance proposals.
     *
     * @param  ExternalProfileSite  $profileSite
     * @param  array  $entries
     * @return void 
     */
    protected function preloadResources(ExternalProfileSite $profileSite, array $entries): void
    {
        $externalResources = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $profileSite->getResourceSite()->value)
            ->whereIn(ExternalResource::ATTRIBUTE_EXTERNAL_ID, Arr::pluck($entries, 'external_id'))
            ->with(ExternalResource::RELATION_ANIME)
            ->get()
            ->mapWithKeys(fn (ExternalResource $resource) => [$resource->external_id => $resource->anime]);

        $this->resources = $externalResources;
    }

    /**
     * Get the animes by the external id.
     *
     * @param  int  $externalId
     * @return Collection<int, Anime>
     */
    protected function getAnimesByExternalId(int $externalId): Collection
    {
        return $this->resources[$externalId] ?? new Collection();
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillAction;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillOtherResourcesAction.
 *
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BackfillAction<TModel>
 */
abstract class BackfillOtherResourcesAction extends BackfillAction
{
    /**
     * Handle action.
     *
     * @return ActionResult
     *
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::beginTransaction();

            $externalLinks = $this->getExternalLinksByAnilistResource();

            if ($externalLinks === null) {
                return new ActionResult(ActionStatus::FAILED);
            }

            $availableSites = $this->getAvailableSites();

            foreach ($externalLinks as $externalLink) {
                $site = $externalLink['site'];
                $language = $externalLink['language'];

                if (!in_array($site, array_keys($availableSites))) continue;
                if (in_array($site, ['Official Site', 'Twitter']) && !in_array($language, ['Japanese', null])) continue;

                if ($this->relation()->getQuery()->where(ExternalResource::ATTRIBUTE_SITE, $availableSites[$site]->value)->exists()) {
                    $nameLocalized = $availableSites[$site]->localize();
                    Log::info("{$nameLocalized} already exists in the model {$this->getModel()->getName()}");
                    continue;
                }

                $resource = $this->getOrCreateResource($externalLink);

                if ($resource !== null) {
                    $this->attachResource($resource);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get or Create Resource from response.
     *
     * @param  mixed  $externalLink
     * @return ExternalResource
     */
    abstract protected function getOrCreateResource(mixed $externalLink): ExternalResource;

    /**
     * Attach External Resource to model.
     *
     * @param  ExternalResource  $resource
     * @return void
     */
    abstract protected function attachResource(ExternalResource $resource): void;

    /**
     * Get the sites to backfill.
     *
     * @return array
     */
    abstract protected function getAvailableSites(): array;

    /**
     * Get the Anilist Resource.
     * 
     * @return ExternalResource|null
     */
    abstract protected function getAnilistResource(): ?ExternalResource;

    /**
     * Get the external links that the Anilist API provides.
     * 
     * @return array|null
     */
    abstract protected function getExternalLinksByAnilistResource(): ?array;
}

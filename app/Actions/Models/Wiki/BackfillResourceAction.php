<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\Models\ActionResult;
use App\Actions\Models\BaseAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillResourceAction.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseAction<TModel>
 */
abstract class BackfillResourceAction extends BaseAction
{
    /**
     * Handle action.
     *
     * @return ActionResult
     *
     * @throws RequestException
     */
    public function handle(): ActionResult
    {
        if ($this->relation()->getQuery()->where(ExternalResource::ATTRIBUTE_SITE, $this->getSite()->value)->exists()) {
            Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Resource of Site '{$this->getSite()->value}'.");

            return new ActionResult(ActionStatus::SKIPPED());
        }

        $resource = $this->getResource();

        if ($resource !== null) {
            $this->attachResource($resource);
        }

        if ($this->relation()->getQuery()->where(ExternalResource::ATTRIBUTE_SITE, $this->getSite()->value)->doesntExist()) {
            return new ActionResult(
                ActionStatus::FAILED(),
                "{$this->label()} '{$this->getModel()->getName()}' has no {$this->getSite()->description} Resource after backfilling. Please review."
            );
        }

        return new ActionResult(ActionStatus::PASSED());
    }

    /**
     * Get or Create Resource from response.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @return ExternalResource
     */
    abstract protected function getOrCreateResource(int $id, string $slug = null): ExternalResource;

    /**
     * Attach External Resource to model.
     *
     * @param  ExternalResource  $resource
     * @return void
     */
    abstract protected function attachResource(ExternalResource $resource): void;

    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    abstract protected function getSite(): ResourceSite;

    /**
     * Query third-party APIs to find Resource mapping.
     *
     * @return ExternalResource|null
     *
     * @throws RequestException
     */
    abstract protected function getResource(): ?ExternalResource;
}

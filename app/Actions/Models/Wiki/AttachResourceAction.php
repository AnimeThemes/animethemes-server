<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

abstract class AttachResourceAction
{
    /**
     * Create a new action instance.
     *
     * @param  ResourceSite[]  $sites
     */
    public function __construct(protected array $sites)
    {
    }

    /**
     * Handle the action.
     *
     * @param  BaseModel  $model
     * @param  array  $data
     * @return void
     */
    public function handle(BaseModel $model, array $data): void
    {
        $resources = $this->getOrCreateResource($data);

        foreach ($resources as $resource) {
            $relation = $this->relation($resource);

            $relation->attach($model);
        }
    }

    /**
     * Get or Create Resource from link field.
     *
     * @param  array  $data
     * @return ExternalResource[]
     */
    protected function getOrCreateResource(array $data): array
    {
        $resources = [];

        foreach ($this->sites as $resourceSite) {
            $link = Arr::get($data, $resourceSite->name);

            if (empty($link)) continue;

            $resource = ExternalResource::query()
                ->where(ExternalResource::ATTRIBUTE_LINK, $link)
                ->first();

            if ($resource === null) {
                $resource = ExternalResource::query()->create([
                    ExternalResource::ATTRIBUTE_EXTERNAL_ID => ResourceSite::parseIdFromLink($link),
                    ExternalResource::ATTRIBUTE_LINK => $link,
                    ExternalResource::ATTRIBUTE_SITE => $resourceSite->value,
                ]);
            }

            $resources[] = $resource;
        }

        return $resources;
    }

    /**
     * Get the relation to the action models.
     *
     * @param  ExternalResource  $resource
     * @return BelongsToMany
     */
    abstract protected function relation(ExternalResource $resource): BelongsToMany;
}

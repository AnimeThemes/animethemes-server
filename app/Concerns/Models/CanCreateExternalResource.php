<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * Trait CanCreateExternalResource.
 */
trait CanCreateExternalResource
{
    use HasLabel;

    /**
     * Get or Create Resource from response.
     *
     * @param  string  $url
     * @param  ResourceSite  $site
     * @param  BaseModel|null  $model
     * @return ExternalResource
     */
    public function createResource(string $url, ResourceSite $site, ?BaseModel $model = null): ExternalResource
    {
        $id = $site::parseIdFromLink($url);

        if ($model instanceof BaseModel) {
            $urlPattern = $site->getUrlCaptureGroups($model);

            if (preg_match($urlPattern, $url, $matches)) {
                $url = $site->formatResourceLink($model::class, intval($matches[2]), $matches[2], $matches[1]);
            }
    
            if ($id !== null) {
                $url = $site->formatResourceLink($model::class, intval($id), $id);
            }
        }

        $resource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $site->value)
            ->where(ExternalResource::ATTRIBUTE_LINK, $url)
            ->orWhere(ExternalResource::ATTRIBUTE_LINK, $url . '/')
            ->first();

        if ($resource === null) {
            Log::info("Creating {$site->localize()} -> '{$url}'");

            $resource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_LINK => $url,
                ExternalResource::ATTRIBUTE_SITE => $site->value,
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
            ]);
        }

        $this->attachResource($resource, $model);

        return $resource;
    }

    /**
     * Try attach the resource.
     *
     * @param  ExternalResource  $resource
     * @param  BaseModel|null  $model
     * @return void
     */
    protected function attachResource(ExternalResource $resource, ?BaseModel $model): void
    {
        if ($model !== null) {
            $resources = $model->resources();

            if ($resources instanceof BelongsToMany) {
                Log::info("Attaching Resource {$resource->getName()} to {$this->privateLabel($model)} {$model->getName()}");
                $resources->attach($resource);
            }
        }
    }
}

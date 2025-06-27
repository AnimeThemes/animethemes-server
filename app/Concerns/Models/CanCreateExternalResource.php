<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;
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
     * @param  (BaseModel&HasResources)|null  $model
     * @return ExternalResource
     */
    public function createResource(string $url, ResourceSite $site, (BaseModel&HasResources)|null $model = null): ExternalResource
    {
        $url = $this->ensureHttpsUrl($url);
        $id = ResourceSite::parseIdFromLink($url);

        if ($model instanceof BaseModel) {
            $urlPattern = $site->getUrlCaptureGroups($model);

            if (preg_match($urlPattern, $url, $matches)) {
                $url = $site->formatResourceLink($model::class, intval($matches[2]), $matches[2], $matches[1]);
            }

            if ($id !== null && $site->usesIdInLink()) {
                $url = $site->formatResourceLink($model::class, intval($id), $id);
            }
        }

        $resource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $site->value)
            ->where(function (Builder $query) use ($url) {
                return $query->where(ExternalResource::ATTRIBUTE_LINK, $url)
                    ->orWhere(ExternalResource::ATTRIBUTE_LINK, $url.'/');
            })
            ->first();

        if ($resource === null) {
            Log::info("Creating External Resource {$site->localize()} -> '{$url}'");

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
     * @param  (BaseModel&HasResources)|null  $model
     * @return void
     */
    protected function attachResource(ExternalResource $resource, (BaseModel&HasResources)|null $model): void
    {
        if ($model !== null) {
            Log::info("Attaching Resource {$resource->getName()} to {$this->privateLabel($model)} {$model->getName()}");
            $model->resources()->attach($resource);
        }
    }

    /**
     * Ensure the URL uses HTTPS.
     *
     * @param  string  $url
     * @return string
     */
    protected function ensureHttpsUrl(string $url): string
    {
        return preg_replace('/^http:/i', 'https:', $url);
    }
}

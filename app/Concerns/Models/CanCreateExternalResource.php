<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Facades\Log;

/**
 * Trait CanCreateExternalResource.
 */
trait CanCreateExternalResource
{
    /**
     * Get or Create Resource from response.
     *
     * @param  class-string<BaseModel>  $model
     * @param  ResourceSite  $site
     * @param  string  $url
     * @return ExternalResource
     */
    public function getOrCreateResource(string $model, ResourceSite $site, string $url): ExternalResource
    {
        $urlPattern = $site->getUrlCaptureGroups(new $model);
        $id = $site::parseIdFromLink($url);

        if (preg_match($urlPattern, $url, $matches)) {
            $url = $site->formatResourceLink($model, intval($matches[2]), $matches[2], $matches[1]);
        }

        if ($id !== null) {
            $url = $site->formatResourceLink($model, intval($id), $id);
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
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $site::parseIdFromLink($url),
            ]);
        }

        return $resource;
    }
}

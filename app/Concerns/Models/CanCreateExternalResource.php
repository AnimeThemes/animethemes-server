<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Uri;

trait CanCreateExternalResource
{
    use HasLabel;

    public function createResource(Uri $uri, ResourceSite $site, (BaseModel&HasResources)|null $model = null): ExternalResource
    {
        $url = $uri->withScheme('https')->__toString();

        $id = ResourceSite::parseIdFromLink($url);
        $id = $id === null ? null : intval($id);

        if ($model instanceof BaseModel) {
            if ($id !== null) {
                $url = $site->formatResourceLink($model::class, $id, strval($id));
            }
        }

        Log::info("Creating External Resource {$site->localize()} -> '{$url}'");

        $resource = $model?->resourcesV2()?->create([
            ExternalResource::ATTRIBUTE_LINK => $url,
            ExternalResource::ATTRIBUTE_SITE => $site->value,
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
        ]);

        return $resource;
    }
}

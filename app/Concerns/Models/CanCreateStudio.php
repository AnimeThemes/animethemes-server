<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait CanCreateStudio
{
    public function getOrCreateStudio(string $name): Studio
    {
        $column = Studio::ATTRIBUTE_NAME;
        $studio = Studio::query()
            ->whereRaw("lower($column) = ?", Str::lower($name))
            ->first();

        if (! $studio instanceof Studio) {
            Log::info("Creating studio '$name'");

            $studio = Studio::query()->create([
                Studio::ATTRIBUTE_NAME => $name,
                Studio::ATTRIBUTE_SLUG => Str::slug($name, '_'),
            ]);
        }

        return $studio;
    }

    public function ensureStudioHasResource(Studio $studio, ResourceSite $site, int $id): void
    {
        $resource = ExternalResource::query()
            ->firstOrCreate([
                ExternalResource::ATTRIBUTE_SITE => $site->value,
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK => $site->formatResourceLink(Studio::class, $id),
            ]);

        $resource->studios()->attach($studio);
    }
}

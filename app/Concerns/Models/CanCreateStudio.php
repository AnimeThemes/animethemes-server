<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait CanCreateStudio
{
    /**
     * Get or create Studio from name (case-insensitive).
     */
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

    /**
     * Ensure Studio has Resource.
     */
    public function ensureStudioHasResource(Studio $studio, ResourceSite $site, int $id): void
    {
        $studioResource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $site->value)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $id)
            ->where(ExternalResource::ATTRIBUTE_LINK, $site->formatResourceLink(Studio::class, $id))
            ->first();

        if (! $studioResource instanceof ExternalResource) {
            Log::info("Creating studio resource with site '{$site->localize()}' and id '$id'");

            $studioResource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK => $site->formatResourceLink(Studio::class, $id),
                ExternalResource::ATTRIBUTE_SITE => $site->value,
            ]);
        }

        if (StudioResource::query()
            ->where($studio->getKeyName(), $studio->getKey())
            ->where($studioResource->getKeyName(), $studioResource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '$studioResource->link' to studio '{$studio->getName()}'");
            $studioResource->studios()->attach($studio);
        }
    }
}

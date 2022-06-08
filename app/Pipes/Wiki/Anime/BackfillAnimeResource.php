<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeResource.
 */
abstract class BackfillAnimeResource extends BackfillAnimePipe
{
    /**
     * Handle an incoming request.
     *
     * @param  User  $user
     * @param  Closure(User): mixed  $next
     * @return mixed
     *
     * @throws RequestException
     */
    public function handle(User $user, Closure $next): mixed
    {
        if ($this->anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, $this->getSite()->value)->exists()) {
            Log::info("Anime '{$this->anime->getName()}' already has Resource of Site '{$this->getSite()->value}'.");

            return $next($user);
        }

        $resource = $this->getResource();

        if ($resource !== null) {
            $this->attachResourceToAnime($resource);
        }

        if ($this->anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, $this->getSite()->value)->doesntExist()) {
            $this->sendNotification(
                $user,
                "Anime '{$this->anime->getName()}' has no {$this->getSite()->description} Resource after backfilling. Please review."
            );
        }

        return $next($user);
    }

    /**
     * Get or Create Resource from response.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @return ExternalResource
     */
    protected function getOrCreateResource(int $id, string $slug = null): ExternalResource
    {
        $resource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $this->getSite()->value)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $id)
            ->whereHas(ExternalResource::RELATION_ANIME, fn (Builder $animeQuery) => $animeQuery->whereKey($this->anime->getKey()))
            ->first();

        if ($resource === null) {
            Log::info("Creating {$this->getSite()->description} resource '$id'");

            $resource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatAnimeResourceLink($this->getSite(), $id, $slug),
                ExternalResource::ATTRIBUTE_SITE => $this->getSite()->value,
            ]);
        }

        return $resource;
    }

    /**
     * Attach Resource to Anime.
     *
     * @param  ExternalResource  $resource
     * @return void
     */
    protected function attachResourceToAnime(ExternalResource $resource): void
    {
        if (AnimeResource::query()
            ->where($this->anime->getKeyName(), $this->anime->getKey())
            ->where($resource->getKeyName(), $resource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '{$resource->getName()}' to anime '{$this->anime->getName()}'");
            $resource->anime()->attach($this->anime);
        }
    }

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

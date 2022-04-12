<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime;

use App\Contracts\Pipes\Pipe;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Nova\Resources\Wiki\Anime as AnimeNovaResource;
use App\Pivots\AnimeResource;
use Illuminate\Http\Client\RequestException;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\Nova;
use RuntimeException;

/**
 * Class MyAnimeListAnidbMapping.
 */
class MyAnimeListAnidbMapping implements Pipe
{
    /**
     * Create new pipe instance.
     *
     * @param Anime $anime
     * @param ExternalResource $resource
     */
    public function __construct(protected Anime $anime, protected ExternalResource $resource)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param User $user
     * @param Closure $next
     * @return mixed
     *
     * @throws RequestException
     */
    public function handle(User $user, Closure $next): mixed
    {
        $anime = $this->anime;
        $resource = $this->resource;

        // A MAL resource is required
        if (!ResourceSite::MAL()->is($resource->site)) {
            throw new RuntimeException("Cannot backfill anime '{$anime->getName()}' with resource '{$resource->getName()}'");
        }

        $this->backfillMalAnidbMapping($anime, $resource);

        if ($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)->doesntExist()) {
            $this->sendNotification($user, $anime);
        }

        return $next($user);
    }

    /**
     * Query Yuna API for MAL-AniDB mapping.
     *
     * @param Anime $anime
     * @param ExternalResource $malResource
     * @return void
     *
     * @throws RequestException
     */
    protected function backfillMalAnidbMapping(Anime $anime, ExternalResource $malResource): void
    {
        $response = Http::get('https://relations.yuna.moe/api/ids', [
            'source' => 'myanimelist',
            'id' => $malResource->external_id,
        ])
            ->throw()
            ->json();

        $anidbId = Arr::get($response, 'anidb');

        // Only proceed if we have a match
        if ($anidbId !== null) {
            $anidbResource = $this->getAnidbResource($anidbId);

            $this->attachAnidbResourceToAnime($anidbResource, $anime);
        }
    }

    /**
     * Get or create AniDB Resource from response.
     *
     * @param  int  $anidbId
     * @return ExternalResource
     */
    protected function getAnidbResource(int $anidbId): ExternalResource
    {
        $anidbResource = ExternalResource::query()
            ->select([ExternalResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_LINK])
            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $anidbId)
            ->first();

        // Create AniDB resource if it doesn't already exist
        if ($anidbResource === null) {
            Log::info("Creating anidb resource '$anidbId'");

            $anidbResource = ExternalResource::factory()->createOne([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $anidbId,
                ExternalResource::ATTRIBUTE_LINK => "https://anidb.net/anime/$anidbId",
                ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
            ]);
        }

        return $anidbResource;
    }

    /**
     * Attach AniDB Resource to Anime.
     *
     * @param  ExternalResource  $anidbResource
     * @param  Anime  $anime
     * @return void
     */
    protected function attachAnidbResourceToAnime(ExternalResource $anidbResource, Anime $anime): void
    {
        if (AnimeResource::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($anidbResource->getKeyName(), $anidbResource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '$anidbResource->link' to anime '$anime->name'");
            $anidbResource->anime()->attach($anime);
        }
    }

    /**
     * Send notification for user to review anime without studios.
     *
     * @param  User  $user
     * @param  Anime  $anime
     * @return void
     */
    protected function sendNotification(User $user, Anime $anime): void
    {
        // Nova requires a relative route without the base path
        $url = route(
            'nova.pages.detail',
            ['resource' => AnimeNovaResource::uriKey(), 'resourceId' => $anime->getKey()],
            false
        );
        $url = Str::remove(Nova::path(), $url);

        $user->notify(
            NovaNotification::make()
                ->icon('flag')
                ->message("Anime '{$anime->getName()}' has no AniDB Resource after backfilling. Please review.")
                ->type(NovaNotification::WARNING_TYPE)
                ->url($url)
        );
    }
}

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
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\Nova;
use RuntimeException;

/**
 * Class MyAnimeListKitsuMapping.
 */
class MyAnimeListKitsuMapping implements Pipe
{
    /**
     * Create new pipe instance.
     *
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     */
    public function __construct(protected Anime $anime, protected ExternalResource $resource)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  User  $user
     * @param  Closure  $next
     * @return mixed
     *
     * @throws RequestException
     */
    public function handle(User $user, Closure $next): mixed
    {
        $anime = $this->anime;
        $resource = $this->resource;

        // A MAL resource is required
        if (! ResourceSite::MAL()->is($resource->site)) {
            throw new RuntimeException("Cannot backfill anime '{$anime->getName()}' with resource '{$resource->getName()}'");
        }

        $this->backfillMalKitsuMapping($anime, $resource);

        if ($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)->doesntExist()) {
            $this->sendNotification($user, $anime);
        }

        return $next($user);
    }

    /**
     * Query Kitsu API for MAL mapping.
     *
     * @param  Anime  $anime
     * @param  ExternalResource  $malResource
     * @return void
     *
     * @throws RequestException
     */
    protected function backfillMalKitsuMapping(Anime $anime, ExternalResource $malResource): void
    {
        $response = Http::contentType('application/vnd.api+json')
            ->accept('application/vnd.api+json')
            ->get('https://kitsu.io/api/edge/mappings', [
                'include' => 'item',
                'filter' => [
                    'externalSite' => 'myanimelist/anime',
                    'externalId' => $malResource->external_id,
                ],
            ])
            ->throw()
            ->json();

        $kitsuResourceData = Arr::get($response, 'data', []);
        $kitsuResourceIncluded = Arr::get($response, 'included', []);

        // Only proceed if we have a single match
        if (count($kitsuResourceData) === 1 && count($kitsuResourceIncluded) === 1) {
            $kitsuId = $kitsuResourceIncluded[0]['id'];
            $kitsuSlug = $kitsuResourceIncluded[0]['attributes']['slug'];

            $kitsuResource = $this->getKitsuResource($kitsuId, $kitsuSlug);

            $this->attachKitsuResourceToAnime($kitsuResource, $anime);
        }
    }

    /**
     * Get or create Kitsu Resource from response.
     *
     * @param  string  $kitsuId
     * @param  string  $kitsuSlug
     * @return ExternalResource
     */
    protected function getKitsuResource(string $kitsuId, string $kitsuSlug): ExternalResource
    {
        $kitsuResource = ExternalResource::query()
            ->select([ExternalResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_LINK])
            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $kitsuId)
            ->first();

        if ($kitsuResource === null) {
            Log::info("Creating kitsu resource '$kitsuId'");

            $kitsuResource = ExternalResource::factory()->createOne([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $kitsuId,
                ExternalResource::ATTRIBUTE_LINK => "https://kitsu.io/anime/$kitsuSlug",
                ExternalResource::ATTRIBUTE_SITE => ResourceSite::KITSU,
            ]);
        }

        return $kitsuResource;
    }

    /**
     * Attach Kitsu Resource to Anime.
     *
     * @param  ExternalResource  $kitsuResource
     * @param  Anime  $anime
     * @return void
     */
    protected function attachKitsuResourceToAnime(ExternalResource $kitsuResource, Anime $anime): void
    {
        if (AnimeResource::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($kitsuResource->getKeyName(), $kitsuResource->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching resource '$kitsuResource->link' to anime '$anime->name'");
            $kitsuResource->anime()->attach($anime);
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
                ->message("Anime '{$anime->getName()}' has no Kitsu Resource after backfilling. Please review.")
                ->type(NovaNotification::WARNING_TYPE)
                ->url($url)
        );
    }
}

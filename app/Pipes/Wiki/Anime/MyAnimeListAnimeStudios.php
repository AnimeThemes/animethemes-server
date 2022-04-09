<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime;

use App\Contracts\Pipes\Pipe;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Nova\Resources\Wiki\Anime as AnimeResource;
use App\Pivots\AnimeStudio;
use App\Pivots\StudioResource;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\Nova;
use RuntimeException;

/**
 * Class MyAnimeListAnimeStudios
 */
class MyAnimeListAnimeStudios implements Pipe
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

        // Do not proceed if we do not have authorization to the MAL API
        $malClientID = Config::get('services.mal.client');
        if ($malClientID === null) {
            throw new RuntimeException('MAL_CLIENT_ID must be configured in your env file.');
        }

        $this->backfillMalAnimeStudios($anime, $resource, $malClientID);

        if ($anime->studios()->doesntExist()) {
            $this->sendNotification($user, $anime);
        }

        return $next($user);
    }

    /**
     * Query MAL API for anime studios.
     *
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     * @param  string  $malClientID
     * @return void
     * @throws RequestException
     */
    protected function backfillMalAnimeStudios(Anime $anime, ExternalResource $resource, string $malClientID): void
    {
        $response = Http::withHeaders(['X-MAL-CLIENT-ID' => $malClientID])
            ->get("https://api.myanimelist.net/v2/anime/$resource->external_id", [
                'fields' => 'studios',
            ])
            ->throw()
            ->json();

        $malStudios = Arr::get($response, 'studios', []);

        foreach ($malStudios as $malStudio) {
            $name = Arr::get($malStudio, 'name');
            $id = Arr::get($malStudio, 'id');
            if (empty($name) || empty($id)) {
                continue;
            }

            $studio = $this->getStudio($name);

            $this->ensureStudioHasMalResource($studio, $id);

            $this->attachAnimeToStudio($studio, $anime);
        }
    }

    /**
     * Get or create Studio from name.
     *
     * @param  string  $name
     * @return Studio
     */
    protected function getStudio(string $name): Studio
    {
        $studio = Studio::query()->firstWhere(Studio::ATTRIBUTE_NAME, $name);
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
     * Ensure Studio has MAL Resource.
     *
     * @param  Studio  $studio
     * @param  int  $id
     * @return void
     */
    protected function ensureStudioHasMalResource(Studio $studio, int $id): void
    {
        $studioResource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $id)
            ->where(ExternalResource::ATTRIBUTE_LINK, ComparisonOperator::LIKE, 'https://myanimelist.net/anime/producer/%')
            ->first();

        if (! $studioResource instanceof ExternalResource) {
            Log::info("Creating studio resource with id '$id'");

            $studioResource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK => "https://myanimelist.net/anime/producer/$id/",
                ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
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

    /**
     * Attach Anime to Studio.
     *
     * @param  Studio  $studio
     * @param  Anime  $anime
     * @return void
     */
    protected function attachAnimeToStudio(Studio $studio, Anime $anime): void
    {
        if (AnimeStudio::query()
            ->where($anime->getKeyName(),$anime->getKey())
            ->where($studio->getKeyName(), $studio->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching studio '{$studio->getName()}' to anime '{$anime->getName()}'");
            $studio->anime()->attach($anime);
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
            ['resource' => AnimeResource::uriKey(), 'resourceId' => $anime->getKey()],
            false
        );
        $url = Str::remove(Nova::path(), $url);

        $user->notify(
            NovaNotification::make()
                ->icon('flag')
                ->message("Anime '{$anime->getName()}' has no studios after backfilling. Please review.")
                ->type(NovaNotification::WARNING_TYPE)
                ->url($url)
        );
    }
}

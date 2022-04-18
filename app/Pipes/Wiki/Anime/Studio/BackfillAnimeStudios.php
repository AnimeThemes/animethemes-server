<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pipes\Wiki\Anime\BackfillAnimePipe;
use App\Pivots\StudioResource;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Orchestra\Parser\Xml\Facade as XmlParser;
use RuntimeException;

/**
 * Class BackfillAnimeStudios.
 */
class BackfillAnimeStudios extends BackfillAnimePipe
{
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
        $studios = $this->getStudios();

        if ($studios->isNotEmpty()) {
            $this->attachStudiosToAnime($studios);
        }

        if ($this->anime->studios()->doesntExist()) {
            $this->sendNotification($user, "Anime '{$this->anime->getName()}' has no Studios after backfilling. Please review.");
        }

        return $next($user);
    }

    /**
     * Query third-party API for Anime Studios.
     *
     * @return Collection<int, Studio>
     *
     * @throws RequestException
     */
    protected function getStudios(): Collection
    {
        $malResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
        if ($malResource instanceof ExternalResource) {
            $studios = $this->getMalAnimeStudios($malResource);
            if ($studios->isNotEmpty()) {
                return $studios;
            }
        }

        $anilistResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
        if ($anilistResource instanceof ExternalResource) {
            $studios = $this->getAnilistAnimeStudios($anilistResource);
            if ($studios->isNotEmpty()) {
                return $studios;
            }
        }

        $kitsuResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU);
        if ($kitsuResource instanceof ExternalResource) {
            $studios = $this->getKitsuAnimeStudios($kitsuResource);
            if ($studios->isNotEmpty()) {
                return $studios;
            }
        }

        $annResource = $this->anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANN);
        if ($annResource instanceof ExternalResource) {
            return $this->getAnnAnimeStudios($annResource);
        }

        return collect();
    }

    /**
     * Query MAL API for Anime Studios.
     *
     * @param  ExternalResource  $malResource
     * @return Collection<int, Studio>
     *
     * @throws RequestException
     */
    protected function getMalAnimeStudios(ExternalResource $malResource): Collection
    {
        $studios = collect();

        $malClientID = Config::get('services.mal.client');
        if ($malClientID === null) {
            throw new RuntimeException('MAL_CLIENT_ID must be configured in your env file.');
        }

        $response = Http::withHeaders(['X-MAL-CLIENT-ID' => $malClientID])
            ->get("https://api.myanimelist.net/v2/anime/$malResource->external_id", [
                'fields' => 'studios',
            ])
            ->throw()
            ->json();

        $malStudios = Arr::get($response, 'studios', []);

        foreach ($malStudios as $malStudio) {
            $name = Arr::get($malStudio, 'name');
            $id = Arr::get($malStudio, 'id');
            if (empty($name) || empty($id)) {
                Log::info("Skipping empty studio of name '$name' and id '$id' for MAL Resource '{$malResource->getName()}'");
                continue;
            }

            $studio = $this->getOrCreateStudio($name);

            $studios->push($studio);

            $this->ensureStudioHasResource($studio, ResourceSite::MAL(), $id);
        }

        return $studios;
    }

    /**
     * Query Anilist API for Anime Studios.
     *
     * @param  ExternalResource  $anilistResource
     * @return Collection<int, Studio>
     *
     * @throws RequestException
     */
    protected function getAnilistAnimeStudios(ExternalResource $anilistResource): Collection
    {
        $studios = collect();

        $query = '
        query ($id: Int) {
            Media (id: $id, type: ANIME) {
                studios (isMain: true) {
                    nodes {
                        id
                        name
                    }
                }
            }
        }
        ';

        $variables = [
            'id' => $anilistResource->external_id,
        ];

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $query,
            'variables' => $variables,
        ])
            ->throw()
            ->json();

        $anilistStudios = Arr::get($response, 'data.Media.studios.nodes', []);

        foreach ($anilistStudios as $anilistStudio) {
            $name = Arr::get($anilistStudio, 'name');
            $id = Arr::get($anilistStudio, 'id');
            if (empty($name) || empty($id)) {
                Log::info("Skipping empty studio of name '$name' and id '$id' for Anilist Resource '{$anilistResource->getName()}'");
                continue;
            }

            $studio = $this->getOrCreateStudio($name);

            $studios->push($studio);

            $this->ensureStudioHasResource($studio, ResourceSite::ANILIST(), $id);
        }

        return $studios;
    }

    /**
     * Query Kitsu API for Anime Studios.
     *
     * @param  ExternalResource  $kitsuResource
     * @return Collection<int, Studio>
     *
     * @throws RequestException
     */
    protected function getKitsuAnimeStudios(ExternalResource $kitsuResource): Collection
    {
        $studios = collect();

        $query = '
        query ($id: ID!) {
            findAnimeById(id: $id) {
                productions(first:20) {
                    nodes {
                        role
                        company {
                            name
                        }
                    }
                }
            }
        }
        ';

        $variables = [
            'id' => $kitsuResource->external_id,
        ];

        $response = Http::post('https://kitsu.io/api/graphql', [
            'query' => $query,
            'variables' => $variables,
        ])
            ->throw()
            ->json();

        $anilistStudios = Arr::get($response, 'data.findAnimeById.productions.nodes', []);

        foreach ($anilistStudios as $anilistStudio) {
            $role = Arr::get($anilistStudio, 'role');
            $name = Arr::get($anilistStudio, 'company.name');
            if ($role !== 'STUDIO' || empty($name)) {
                Log::info("Skipping production company of name '$name' and role '$role' for Anilist Resource '{$kitsuResource->getName()}'");
                continue;
            }

            $studio = $this->getOrCreateStudio($name);

            $studios->push($studio);
        }

        return $studios;
    }

    /**
     * Query ANN API for Anime Studios.
     *
     * @param  ExternalResource  $annResource
     * @return Collection<int, Studio>
     *
     * @throws RequestException
     */
    protected function getAnnAnimeStudios(ExternalResource $annResource): Collection
    {
        $studios = collect();

        $response = Http::get("https://cdn.animenewsnetwork.com/encyclopedia/api.xml?anime=$annResource->external_id")
            ->throw()
            ->body();

        $xml = XmlParser::extract($response);

        $annCredits = $xml->parse([
            'credits' => [
                'uses' => 'anime.credit[task,company>name,company::id>id]',
            ],
        ]);

        $annStudios = Arr::get($annCredits, 'credits', []);
        foreach ($annStudios as $annStudio) {
            $task = Arr::get($annStudio, 'task');
            $name = Arr::get($annStudio, 'name');
            $id = Arr::get($annStudio, 'id');
            if ($task !== 'Animation Production' || empty($name) || empty($id)) {
                Log::info("Skipping production company of task '$task' and name '$name' and id '$id'");
                continue;
            }

            $studio = $this->getOrCreateStudio($name);

            $studios->push($studio);

            $this->ensureStudioHasResource($studio, ResourceSite::ANN(), intval($id));
        }

        return $studios;
    }

    /**
     * Get or create Studio from name (case-insensitive).
     *
     * @param  string  $name
     * @return Studio
     */
    protected function getOrCreateStudio(string $name): Studio
    {
        $column = Studio::ATTRIBUTE_NAME;
        $studio = Studio::query()
            ->where(DB::raw("lower($column)"), Str::lower($name))
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
     *
     * @param  Studio  $studio
     * @param  ResourceSite  $site
     * @param  int  $id
     * @return void
     */
    protected function ensureStudioHasResource(Studio $studio, ResourceSite $site, int $id): void
    {
        $studioResource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_SITE, $site->value)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $id)
            ->first();

        if (! $studioResource instanceof ExternalResource) {
            Log::info("Creating studio resource with site '$site->value' and id '$id'");

            $studioResource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatStudioResourceLink($site, $id),
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

    /**
     * Attach Studios to Anime.
     *
     * @param  Collection<int, Studio>  $studios
     * @return void
     */
    protected function attachStudiosToAnime(Collection $studios): void
    {
        $results = $this->anime->studios()->sync($studios->pluck(Studio::ATTRIBUTE_ID));

        if (count($results['attached'])) {
            Log::info("Attached Studios to anime '{$this->anime->getName()}'", $results['attached']);
        }
        if (count($results['updated'])) {
            Log::info("Updated Studios for anime '{$this->anime->getName()}'", $results['updated']);
        }
        if (count($results['detached'])) {
            Log::info("Detached Studios from anime '{$this->anime->getName()}'", $results['detached']);
        }
    }
}

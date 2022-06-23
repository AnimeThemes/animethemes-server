<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime as AnimeResource;
use App\Pipes\Wiki\BackfillStudios;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Orchestra\Parser\Xml\Facade as XmlParser;
use RuntimeException;

/**
 * Class BackfillAnimeStudios.
 *
 * @extends BackfillStudios<Anime>
 */
class BackfillAnimeStudios extends BackfillStudios
{
    /**
     * Create a new pipe instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model passed into the pipeline.
     *
     * @return Anime
     */
    public function getModel(): Anime
    {
        return $this->model;
    }

    /**
     * Get the relation to studios.
     *
     * @return BelongsToMany
     */
    protected function relation(): BelongsToMany
    {
        return $this->getModel()->studios();
    }

    /**
     * Get the nova resource.
     *
     * @return class-string<Resource>
     */
    protected function resource(): string
    {
        return AnimeResource::class;
    }

    /**
     * Query third-party API for Anime Studios.
     *
     * @return Studio[]
     *
     * @throws RequestException
     */
    protected function getStudios(): array
    {
        $malResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
        if ($malResource instanceof ExternalResource) {
            $studios = $this->getMalAnimeStudios($malResource);
            if (! empty($studios)) {
                return $studios;
            }
        }

        $anilistResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
        if ($anilistResource instanceof ExternalResource) {
            $studios = $this->getAnilistAnimeStudios($anilistResource);
            if (! empty($studios)) {
                return $studios;
            }
        }

        $kitsuResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU);
        if ($kitsuResource instanceof ExternalResource) {
            $studios = $this->getKitsuAnimeStudios($kitsuResource);
            if (! empty($studios)) {
                return $studios;
            }
        }

        $annResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANN);
        if ($annResource instanceof ExternalResource) {
            return $this->getAnnAnimeStudios($annResource);
        }

        return [];
    }

    /**
     * Query MAL API for Anime Studios.
     *
     * @param  ExternalResource  $malResource
     * @return Studio[]
     *
     * @throws RequestException
     */
    protected function getMalAnimeStudios(ExternalResource $malResource): array
    {
        $studios = [];

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

            $studios[] = $studio;

            $this->ensureStudioHasResource($studio, ResourceSite::MAL(), $id);
        }

        return $studios;
    }

    /**
     * Query Anilist API for Anime Studios.
     *
     * @param  ExternalResource  $anilistResource
     * @return Studio[]
     *
     * @throws RequestException
     */
    protected function getAnilistAnimeStudios(ExternalResource $anilistResource): array
    {
        $studios = [];

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

            $studios[] = $studio;

            $this->ensureStudioHasResource($studio, ResourceSite::ANILIST(), $id);
        }

        return $studios;
    }

    /**
     * Query Kitsu API for Anime Studios.
     *
     * @param  ExternalResource  $kitsuResource
     * @return Studio[]
     *
     * @throws RequestException
     */
    protected function getKitsuAnimeStudios(ExternalResource $kitsuResource): array
    {
        $studios = [];

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

            $studios[] = $studio;
        }

        return $studios;
    }

    /**
     * Query ANN API for Anime Studios.
     *
     * @param  ExternalResource  $annResource
     * @return Studio[]
     *
     * @throws RequestException
     */
    protected function getAnnAnimeStudios(ExternalResource $annResource): array
    {
        $studios = [];

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

            $studios[] = $studio;

            $this->ensureStudioHasResource($studio, ResourceSite::ANN(), intval($id));
        }

        return $studios;
    }

    /**
     * Attach Studios.
     *
     * @param  Studio[]  $studios
     * @return void
     */
    protected function attachStudios(array $studios): void
    {
        Log::info("Attaching studios to {$this->label()} '{$this->getModel()->getName()}'");
        $this->relation()->attach(Arr::pluck($studios, Studio::ATTRIBUTE_ID));
    }
}

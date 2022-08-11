<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\Studio;

use App\Actions\Models\Wiki\BackfillStudiosAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class BackfillAnimeStudiosAction.
 *
 * @extends BackfillStudiosAction<Anime>
 */
class BackfillAnimeStudiosAction extends BackfillStudiosAction
{
    /**
     * Create a new action instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model the action is handling.
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
            return $this->getKitsuAnimeStudios($kitsuResource);
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

        $response = Http::withHeaders(['X-MAL-CLIENT-ID' => Config::get('services.mal.client')])
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

        $kitsuStudios = Arr::get($response, 'data.findAnimeById.productions.nodes', []);

        foreach ($kitsuStudios as $kitsuStudio) {
            $role = Arr::get($kitsuStudio, 'role');
            $name = Arr::get($kitsuStudio, 'company.name');
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

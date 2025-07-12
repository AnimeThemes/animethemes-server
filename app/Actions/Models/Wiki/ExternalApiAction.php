<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Contracts\Actions\Models\Wiki\BackfillImages;
use App\Contracts\Actions\Models\Wiki\BackfillResources;
use App\Contracts\Actions\Models\Wiki\BackfillSynonyms;
use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * Class ExternalApiAction.
 */
abstract class ExternalApiAction
{
    /**
     * The response of the request.
     *
     * @var array|null
     */
    public ?array $response = null;

    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    abstract public function getSite(): ResourceSite;

    /**
     * Set the response after the request.
     *
     * @param  BelongsToMany  $resources
     * @return static
     */
    abstract public function handle(BelongsToMany $resources): static;

    /**
     * Get the mapped resources.
     *
     * @return string[]
     */
    public function getResources(): array
    {
        $resources = [];

        if ($this instanceof BackfillResources && $this->response) {
            foreach ($this->getResourcesMapping() as $site => $key) {
                if (Arr::get($this->response, $key) !== null) {
                    $resources[$site] = Arr::get($this->response, $key);
                }
            }
        }

        return $resources;
    }

    /**
     * Get the mapped images.
     *
     * @return string[]
     */
    public function getImages(): array
    {
        $images = [];

        if ($this instanceof BackfillImages && $this->response) {
            foreach ($this->getImagesMapping() as $facet => $key) {
                if (Arr::get($this->response, $key) !== null) {
                    $images[$facet] = Arr::get($this->response, $key);
                }
            }
        }

        return $images;
    }

    /**
     * Get the mapped synonyms.
     *
     * @return array<int|string, string>
     */
    public function getSynonyms(): array
    {
        $synonyms = [];

        if ($this instanceof BackfillSynonyms && $this->response) {
            foreach ($this->getSynonymsMapping() as $type => $key) {
                $synonyms[$type] = Arr::get($this->response, $key);
            }
        }

        return $synonyms;
    }
}

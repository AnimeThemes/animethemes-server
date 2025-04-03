<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * Class ApiAction.
 */
abstract class ApiAction
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
     * Create a new action instance.
     *
     */
    public function __construct()
    {
    }

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
     * @return array<int, string>
     */
    public function getResources(): array
    {
        $resources = [];

        if ($this->response) {
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
     * @return array<int, string>
     */
    public function getImages(): array
    {
        $images = [];

        if ($this->response) {
            foreach ($this->getImagesMapping() as $facet => $key) {
                if (Arr::get($this->response, $key) !== null) {
                    $images[$facet] = Arr::get($this->response, $key);
                }
            }
        }

        return $images;
    }

    /**
     * Get the mapped studios.
     *
     * @return array
     */
    public function getStudios(): array
    {
        return [];
    }

    /**
     * Get the mapped synonyms.
     *
     * @return array
     */
    public function getSynonyms(): array
    {
        return [];
    }

    /**
     * Get the mapping for the resources.
     *
     * @return array<int, string>
     */
    abstract protected function getResourcesMapping(): array;

    /**
     * Get the mapping for the images.
     *
     * @return array<int, string>
     */
    abstract protected function getImagesMapping(): array;
}

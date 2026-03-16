<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Contracts\Actions\Models\Wiki\BackfillImages;
use App\Contracts\Actions\Models\Wiki\BackfillResources;
use App\Contracts\Actions\Models\Wiki\BackfillSynonyms;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\SynonymType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class ExternalApiAction
{
    public ?array $response = null;

    abstract public function getSite(): ResourceSite;

    /**
     * Set the response after the request.
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
     * @return Collection<int, string>
     */
    public function getSynonyms(): Collection
    {
        $synonyms = collect();

        if ($this instanceof BackfillSynonyms && $this->response) {
            foreach ($this->getSynonymsMapping() as $type => $key) {
                $synonyms->put($type, Arr::get($this->response, $key));
            }
        }

        return $synonyms
            ->filter(fn (?string $text): bool => filled($text))
            ->reject(function (string $text, int $type) use ($synonyms) {
                if ($type !== SynonymType::OTHER->value) {
                    return false;
                }

                return $synonyms
                    ->except([SynonymType::OTHER->value])
                    ->containsStrict($text);
            });
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Models;

use App\Actions\ActionResult;
use App\Actions\Models\Wiki\ExternalApiAction;
use App\Concerns\Models\CanCreateExternalResource;
use App\Concerns\Models\CanCreateImage;
use App\Contracts\Models\HasImages;
use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

/**
 * Class BackfillWikiAction.
 */
abstract class BackfillWikiAction
{
    use CanCreateExternalResource;
    use CanCreateImage;

    final public const RESOURCES = 'resources';
    final public const IMAGES = 'images';

    /**
     * Create a new action instance.
     *
     * @param  BaseModel  $model
     * @param  array  $toBackfill
     */
    public function __construct(protected BaseModel $model, protected array $toBackfill) {}

    /**
     * Handle the action.
     *
     * @return ActionResult
     */
    abstract public function handle(): ActionResult;

    /**
     * Get the external API actions available for the backfill action.
     *
     * @return array<ExternalApiAction>
     */
    abstract protected function getExternalApiActions(): array;

    /**
     * Create the resources given the response.
     *
     * @param  ExternalApiAction  $api
     * @return void
     */
    protected function forResources(ExternalApiAction $api): void
    {
        $toBackfill = $this->toBackfill[self::RESOURCES];

        foreach ($api->getResources() as $site => $url) {
            $site = ResourceSite::from($site);

            if (! in_array($site, $toBackfill)) {
                Log::info("Resource {$site->localize()} should not be backfilled for {$this->label()} {$this->getModel()->getName()}");
                continue;
            }

            if ($this->getModel()->resources()->getQuery()->where(ExternalResource::ATTRIBUTE_SITE, $site->value)->exists()) {
                Log::info("Resource {$site->localize()} already exists for {$this->label()} {$this->getModel()->getName()}");
                continue;
            }

            $this->createResource(Uri::of($url), $site, $this->getModel());

            $this->backfilled($site, self::RESOURCES);
        }
    }

    /**
     * Create the images given the response.
     *
     * @param  ExternalApiAction  $api
     * @return void
     */
    protected function forImages(ExternalApiAction $api): void
    {
        $toBackfill = $this->toBackfill[self::IMAGES];

        foreach ($api->getImages() as $facet => $url) {
            $facet = ImageFacet::from($facet);

            if (! in_array($facet, $toBackfill)) {
                Log::info("Skipping {$facet->localize()} for {$this->label()} {$this->getModel()->getName()}");
                continue;
            }

            if ($this->getModel()->images()->getQuery()->where(Image::ATTRIBUTE_FACET, $facet->value)->exists()) {
                Log::info("Image {$facet->localize()} already exists for {$this->label()} {$this->getModel()->getName()}");
                continue;
            }

            $this->createImageFromUrl($url, $facet, $this->getModel());

            $this->backfilled($facet, self::IMAGES);
        }
    }

    /**
     * Remove element already backfilled.
     *
     * @param  mixed  $enum
     * @param  string  $scope
     * @return void
     */
    protected function backfilled(mixed $enum, string $scope): void
    {
        $index = array_search($enum, $this->toBackfill[$scope]);

        if ($index !== false) {
            unset($this->toBackfill[$scope][$index]);
        }
    }

    /**
     * Get the model for the action.
     *
     * @return BaseModel&HasResources&HasImages
     */
    abstract protected function getModel(): BaseModel;

    /**
     * Get the human-friendly label for the underlying model.
     *
     * @return string
     */
    protected function label(): string
    {
        return Str::headline(class_basename($this->getModel()));
    }
}

<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Image as NovaImage;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AttachImageAction.
 */
abstract class AttachImageAction extends Action
{
    /**
     * Create a new action instance.
     *
     * @param  ImageFacet[]  $facets
     */
    public function __construct(protected array $facets)
    {
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.models.wiki.attach_image.name');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return Collection
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {
        $images = $this->createImages($fields, $models->first());

        foreach ($images as $image) {
            if (in_array($image->facet, [ImageFacet::GRILL, ImageFacet::DOCUMENT])) continue;

            $relation = $this->relation($image);

            $relation->attach($models);
        }

        return $models;
    }

    /**
     * Create the images.
     *
     * @param  ActionFields  $fields
     * @param  Model|null  $model
     * @return Image[]
     */
    protected function createImages(ActionFields $fields, ?Model $model): array
    {
        $images = [];

        foreach ($this->facets as $facet) {
            $image = $fields->get($facet->name);

            if (empty($image)) continue;
    
            $fs = Storage::disk(Config::get('image.disk'));
    
            $fsFile = $fs->putFile($this->path($facet, $model), $image);

            $image = Image::query()->create([
                Image::ATTRIBUTE_FACET => $facet->value,
                Image::ATTRIBUTE_PATH => $fsFile,
            ]);

            $images[] = $image;
        }

        return $images;
    }

    /**
     * Path to storage image in filesystem.
     *
     * @param  ImageFacet  $facet
     * @param  Model|null  $model
     * @return string
     */
    protected function path(ImageFacet $facet, ?Model $model): string
    {
        if (in_array($facet, [ImageFacet::GRILL, ImageFacet::DOCUMENT])) {
            return Str::of(Str::kebab($facet->localize()))
                ->__toString();
        }
        
        return Str::of(Str::kebab(class_basename($model)))
            ->append(DIRECTORY_SEPARATOR)
            ->append(Str::kebab($facet->localize()))
            ->__toString();
    }

    /**
     * Get the relation to the action models.
     *
     * @param  Image  $image
     * @return BelongsToMany|Image
     */
    abstract protected function relation(Image $image): BelongsToMany|Image;

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        $fields = [];
        $model = $request->findModelQuery()->first();

        foreach ($this->facets as $facet) {
            if ($model instanceof Anime || $model instanceof Artist || $model instanceof Studio) {
                $images = $model->images();
                if ($images->where(Image::ATTRIBUTE_FACET, $facet->value)->exists()) continue;
            }

            $fields[] = NovaImage::make($facet->localize(), $facet->name);
        }

        return array_merge(
            parent::fields($request),
            $fields
        );
    }
}

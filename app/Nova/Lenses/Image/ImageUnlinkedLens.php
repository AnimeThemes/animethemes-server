<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use App\Nova\Lenses\BaseLens;
use BenSampo\Enum\Enum;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image as NovaImage;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ImageUnlinkedLens.
 */
class ImageUnlinkedLens extends BaseLens
{
    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.image_unlinked_lens');
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereNot(Image::ATTRIBUTE_FACET, ImageFacet::GRILL)
            ->whereDoesntHave(Image::RELATION_ANIME)
            ->whereDoesntHave(Image::RELATION_ARTISTS)
            ->whereDoesntHave(Image::RELATION_STUDIOS);
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), Image::ATTRIBUTE_ID)
                ->sortable(),

            Select::make(__('nova.facet'), Image::ATTRIBUTE_FACET)
                ->options(ImageFacet::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->filterable(),

            NovaImage::make(__('nova.image'), Image::ATTRIBUTE_PATH)
                ->disk('images'),
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function uriKey(): string
    {
        return 'image-unlinked-lens';
    }
}

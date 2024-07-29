<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Studio\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Nova\Actions\Models\Wiki\Studio\AttachStudioImageAction;
use App\Nova\Lenses\Studio\StudioLens;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class StudioCoverLargeLens.
 */
class StudioCoverLargeLens extends StudioLens
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
        return __('nova.lenses.studio.images.name', ['facet' => ImageFacet::COVER_LARGE->localize()]);
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Studio::RELATION_IMAGES, function (Builder $imageQuery) {
            $imageQuery->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE->value);
        });
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
        return [
            (new AttachStudioImageAction([ImageFacet::COVER_LARGE]))
                ->confirmButtonText(__('nova.actions.models.wiki.attach_image.confirmButtonText'))
                ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                ->exceptOnIndex()
                ->canSeeWhen('create', Image::class),
        ];
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
        return 'studio-cover-large-lens';
    }
}

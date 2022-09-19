<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use App\Nova\Lenses\BaseLens;
use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image as NovaImage;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
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
        return __('nova.lenses.image.unlinked.name');
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
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), Image::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Select::make(__('nova.fields.image.facet.name'), Image::ATTRIBUTE_FACET)
                ->options(ImageFacet::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            NovaImage::make(__('nova.fields.image.image.name'), Image::ATTRIBUTE_PATH, Config::get('image.disk'))
                ->showOnPreview(),

            Text::make(__('nova.fields.image.path.name'), Image::ATTRIBUTE_PATH)
                ->copyable()
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
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

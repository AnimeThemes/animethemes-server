<?php

declare(strict_types=1);

namespace App\Nova\Lenses\ExternalResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use App\Nova\Lenses\BaseLens;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ExternalResourceUnlinkedLens.
 */
class ExternalResourceUnlinkedLens extends BaseLens
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
        return __('nova.lenses.external_resource.unlinked.name');
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(ExternalResource::RELATION_ANIME)
            ->whereDoesntHave(ExternalResource::RELATION_ARTISTS)
            ->whereDoesntHave(ExternalResource::RELATION_SONG)
            ->whereDoesntHave(ExternalResource::RELATION_STUDIOS);
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
            ID::make(__('nova.fields.base.id'), ExternalResource::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Select::make(__('nova.fields.external_resource.site.name'), ExternalResource::ATTRIBUTE_SITE)
                ->options(ResourceSite::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => ResourceSite::tryFrom($enumValue)?->localize())
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            URL::make(__('nova.fields.external_resource.link.name'), ExternalResource::ATTRIBUTE_LINK)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.fields.external_resource.external_id.name'), ExternalResource::ATTRIBUTE_EXTERNAL_ID)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

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
        return 'external-resource-unlinked-lens';
    }
}

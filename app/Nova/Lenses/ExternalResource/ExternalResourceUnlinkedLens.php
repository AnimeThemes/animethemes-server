<?php

declare(strict_types=1);

namespace App\Nova\Lenses\ExternalResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use App\Nova\Lenses\BaseLens;
use BenSampo\Enum\Enum;
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
        return __('nova.resource_unlinked_lens');
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
            ID::make(__('nova.id'), ExternalResource::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Select::make(__('nova.site'), ExternalResource::ATTRIBUTE_SITE)
                ->options(ResourceSite::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            URL::make(__('nova.link'), ExternalResource::ATTRIBUTE_LINK)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.external_id'), ExternalResource::ATTRIBUTE_EXTERNAL_ID)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            DateTime::make(__('nova.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
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

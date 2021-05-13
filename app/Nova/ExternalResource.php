<?php

namespace App\Nova;

use App\Enums\ResourceSite;
use App\Rules\ResourceSiteDomainRule;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

class ExternalResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\ExternalResource::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'link';

    /**
     * The logical group associated with the resource.
     *
     * @return array|string|null
     */
    public static function group()
    {
        return __('nova.wiki');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'link',
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label()
    {
        return __('nova.external_resources');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return __('nova.external_resource');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('nova.id'), 'resource_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Select::make(__('nova.site'), 'site')
                ->options(ResourceSite::asSelectArray())
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('required', (new EnumValue(ResourceSite::class, false))->__toString())
                ->help(__('nova.resource_site_help')),

            Url::make(__('nova.link'), 'link')
                ->sortable()
                ->rules('required', 'max:192', 'url', new ResourceSiteDomainRule($request->input('site')))
                ->creationRules('unique:resource,link')
                ->updateRules('unique:resource,link,{{resourceId}},resource_id')
                ->help(__('nova.resource_link_help'))
                ->alwaysClickable(),

            Number::make(__('nova.external_id'), 'external_id')
                ->nullable()
                ->sortable()
                ->rules('nullable', 'integer')
                ->help(__('nova.resource_external_id_help')),

            BelongsToMany::make(__('nova.artists'), 'Artists', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.anime'), 'Anime', Anime::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            AuditableLog::make(),
        ];
    }

    /**
     * @return array
     */
    protected function timestamps()
    {
        return [
            DateTime::make(__('nova.created_at'), 'created_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            DateTime::make(__('nova.updated_at'), 'updated_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            DateTime::make(__('nova.deleted_at'), 'deleted_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\ExternalResourceSiteFilter,
            new Filters\CreatedStartDateFilter,
            new Filters\CreatedEndDateFilter,
            new Filters\UpdatedStartDateFilter,
            new Filters\UpdatedEndDateFilter,
            new Filters\DeletedStartDateFilter,
            new Filters\DeletedEndDateFilter,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            new Lenses\ExternalResourceUnlinkedLens,
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}

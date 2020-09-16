<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use App\Enums\ResourceType;
use App\Rules\ResourceTypeDomain;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Inspheric\Fields\Url;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

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
     * @var string
     */
    public static function group() {
        return __('nova.wiki');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'link'
    ];

    public static function label()
    {
        return __('nova.external_resources');
    }

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
                ->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Select::make(__('nova.type'), 'type')
                ->options(ResourceType::asSelectArray())
                ->resolveUsing(function ($enum) {
                    return $enum ? $enum->value : null;
                })
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('required', new EnumValue(ResourceType::class, false))
                ->help(__('nova.resource_type_help')),

            Url::make(__('nova.link'), 'link')
                ->sortable()
                ->rules('required', 'max:192', 'url', new ResourceTypeDomain($request->input('type')))
                ->creationRules('unique:resource,link')
                ->updateRules('unique:resource,link,{{resourceId}},resource_id')
                ->help(__('nova.resource_link_help'))
                ->alwaysClickable(),

            Number::make(__('nova.external_id'), 'external_id')
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
                    ];
                }),

            BelongsToMany::make(__('nova.anime'), 'Anime', Anime::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.resource_as_help')),
                    ];
                }),

            AuditableLog::make(),
        ];
    }

    protected function timestamps() {
        return [
            DateTime::make(__('nova.created_at'), 'created_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            DateTime::make(__('nova.updated_at'), 'updated_at')
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
            new Filters\ExternalResourceTypeFilter,
            new Filters\RecentlyCreatedFilter,
            new Filters\RecentlyUpdatedFilter
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
            new Lenses\ExternalResourceUnlinkedLens
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

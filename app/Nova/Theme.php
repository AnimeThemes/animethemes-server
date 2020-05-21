<?php

namespace App\Nova;

use App\Enums\ThemeType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\Enum\Enum;

class Theme extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Theme::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'slug';

    public static function label()
    {
        return __('nova.themes');
    }

    public static function singularLabel()
    {
        return __('nova.theme');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'slug'
    ];

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('nova.id'), 'theme_id')
                ->sortable(),

            BelongsTo::make(__('nova.anime'), 'Anime')
               ->readonly(),
            
            new Panel(__('nova.timestamps'), $this->timestamps()),

            Enum::make(__('nova.type'), 'type')
                ->attachEnum(ThemeType::class)
                ->sortable()
                ->rules('required', new EnumValue(ThemeType::class, false))
                ->help(__('nova.theme_type_help')),

            Number::make(__('nova.sequence'), 'sequence')
                ->sortable()
                ->rules('nullable', 'integer')
                ->help(__('nova.theme_sequence_help')),

            Text::make(__('nova.group'), 'group')
                ->sortable()
                ->rules('nullable', 'max:192')
                ->help(__('nova.theme_group_help')),

            BelongsTo::make(__('nova.song'), 'Song')
                ->sortable()
                ->searchable()
                ->showCreateRelationButton(),

            HasMany::make(__('nova.entries'), 'Entries'),
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
            new Filters\ThemeTypeFilter
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
        return [];
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

<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

class Entry extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Entry::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'entry_id';

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label()
    {
        return __('nova.entries');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return __('nova.entry');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'entry_id',
    ];

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            BelongsTo::make(__('nova.anime'), 'Anime', Anime::class)
                ->hideFromIndex(function () use ($request) {
                    return Video::uriKey() !== $request->viaResource;
                })
                ->readonly(),

            BelongsTo::make(__('nova.theme'), 'Theme', Theme::class)
                ->readonly(),

            ID::make(__('nova.id'), 'entry_id')
                ->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Number::make(__('nova.version'), 'version')
                ->sortable()
                ->rules('nullable', 'integer')
                ->help(__('nova.entry_version_help')),

            Text::make(__('nova.episodes'), 'episodes')
                ->sortable()
                ->rules('nullable', 'max:192')
                ->help(__('nova.entry_episodes_help')),

            Boolean::make(__('nova.nsfw'), 'nsfw')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.entry_nsfw_help')),

            Boolean::make(__('nova.spoiler'), 'spoiler')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.entry_spoiler_help')),

            Text::make(__('nova.notes'), 'notes')
                ->sortable()
                ->rules('nullable', 'max:192')
                ->help(__('nova.entry_notes_help')),

            BelongsToMany::make(__('nova.videos'), 'Videos', Video::class)
                ->searchable(),

            AuditableLog::make(),
        ];
    }

    protected function timestamps() : array
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
            new Filters\EntryNsfwFilter,
            new Filters\EntrySpoilerFilter,
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

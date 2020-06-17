<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Artist extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Artist::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static function group() {
        return __('nova.wiki');
    }

    public static function label()
    {
        return __('nova.artists');
    }

    public static function singularLabel()
    {
        return __('nova.artist');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('nova.id'), 'artist_id')
                ->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->rules('required', 'max:192')
                ->help(__('nova.artist_name_help')),

            Text::make(__('nova.alias'), 'alias')
                ->sortable()
                ->rules('required', 'max:192', 'alpha_dash')
                ->creationRules('unique:artist,alias')
                ->updateRules('unique:artist,alias,{{resourceId}},artist_id')
                ->help(__('nova.artist_alias_help')),

            BelongsToMany::make(__('nova.songs'), 'Songs', Song::class)
                ->searchable()
                ->withSubtitles() //TODO: doesn't work, open issue
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.as_help')),
                    ];
                }),

            BelongsToMany::make(__('nova.external_resources'), 'ExternalResources', ExternalResource::class)
                ->searchable(),

            BelongsToMany::make(__('nova.members'), 'Members', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.as_help')),
                    ];
                }),

            BelongsToMany::make(__('nova.groups'), 'Groups', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.as_help')),
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
        return [
            (new Metrics\NewArtists)->width('1/2'),
            (new Metrics\ArtistsPerDay)->width('1/2'),
        ];
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
            new Lenses\ArtistAniDbResourceLens,
            new Lenses\ArtistAnilistResourceLens,
            new Lenses\ArtistAnnResourceLens,
            new Lenses\ArtistMalResourceLens,
            new Lenses\ArtistSongLens
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

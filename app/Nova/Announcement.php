<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Announcement extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Announcement::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'alias';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static function group() {
        return __('nova.admin');
    }

    public static function label()
    {
        return __('nova.announcements');
    }

    public static function singularLabel()
    {
        return __('nova.announcement');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'alias',
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
            ID::make(__('nova.id'), 'id')->sortable(),

            Text::make(__('nova.alias'), 'alias')
                ->sortable()
                ->rules('required', 'max:192', 'alpha_dash')
                ->creationRules('unique:announcements,alias')
                ->updateRules('unique:announcements,alias,{{resourceId}},id')
                ->help(__('nova.announcement_alias_help')),

            Code::make(__('nova.content'), 'content')
                ->sortable()
                ->rules('required', 'max:65535')
                ->options(['theme' => 'base16-light']),

            AuditableLog::make(),
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
        return [];
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

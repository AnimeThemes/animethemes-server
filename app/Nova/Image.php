<?php

namespace App\Nova;

use App\Enums\ImageFacet;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image as NovaImage;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

class Image extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Image::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'image_id';

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
        'image_id',
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label()
    {
        return __('nova.images');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return __('nova.image');
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
            ID::make(__('nova.id'), 'image_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            new Panel(__('nova.file_properties'), $this->fileProperties()),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Select::make(__('nova.facet'), 'facet')
                ->options(ImageFacet::asSelectArray())
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('required', (new EnumValue(ImageFacet::class, false))->__toString())
                ->help(__('nova.image_facet_help')),

            NovaImage::make(__('nova.image'), 'path', 'images', new StoreImage)
                ->creationRules('required'),

            BelongsToMany::make(__('nova.anime'), 'Anime', Anime::class)
                ->searchable()
                ->fields(function () {
                    return [
                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.artists'), 'Artists', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
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
    protected function fileProperties()
    {
        return [
            Text::make(__('nova.path'), 'path')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            Number::make(__('nova.size'), 'size')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            Text::make(__('nova.mimetype'), 'mimetype')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),
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
            new Filters\ImageFacetFilter,
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
            new Lenses\ImageUnlinkedLens,
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

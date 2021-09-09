<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Nova\Filters\Wiki\Image\ImageFacetFilter;
use App\Nova\Lenses\Image\ImageUnlinkedLens;
use App\Nova\Resources\Resource;
use App\Services\Nova\Resources\StoreImage;
use BenSampo\Enum\Enum;
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

/**
 * Class Image.
 */
class Image extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \App\Models\Wiki\Image::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'image_id';

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
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
     * Determine if this resource uses Laravel Scout.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function usesScout(): bool
    {
        return false;
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.images');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function singularLabel(): string
    {
        return __('nova.image');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'image_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.file_properties'), $this->fileProperties()),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Select::make(__('nova.facet'), 'facet')
                ->options(ImageFacet::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum?->description;
                })
                ->sortable()
                ->rules(['required', (new EnumValue(ImageFacet::class, false))->__toString()])
                ->help(__('nova.image_facet_help')),

            NovaImage::make(__('nova.image'), 'path', 'images', new StoreImage())
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
    protected function fileProperties(): array
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
     * Get the filters available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            [
                new ImageFacetFilter(),
            ],
            parent::filters($request)
        );
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function lenses(Request $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new ImageUnlinkedLens(),
            ]
        );
    }
}

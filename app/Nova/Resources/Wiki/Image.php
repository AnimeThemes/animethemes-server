<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image as ImageModel;
use App\Nova\Lenses\Image\ImageUnlinkedLens;
use App\Nova\Resources\Resource;
use App\Pivots\BasePivot;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image as NovaImage;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

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
    public static string $model = ImageModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = ImageModel::ATTRIBUTE_ID;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        return (string) data_get($this, ImageModel::ATTRIBUTE_PATH);
    }

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
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new Column(ImageModel::ATTRIBUTE_ID),
            new Column(ImageModel::ATTRIBUTE_PATH),
        ];
    }

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
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), ImageModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Select::make(__('nova.facet'), ImageModel::ATTRIBUTE_FACET)
                ->options(ImageFacet::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(ImageFacet::class, false))->__toString()])
                ->help(__('nova.image_facet_help'))
                ->showOnPreview()
                ->filterable(),

            NovaImage::make(__('nova.image'), ImageModel::ATTRIBUTE_PATH, 'images')
                ->creationRules('required')
                ->showOnPreview(),

            BelongsToMany::make(__('nova.anime'), ImageModel::RELATION_ANIME, Anime::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating(),

                    DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating(),
                ]),

            BelongsToMany::make(__('nova.artists'), ImageModel::RELATION_ARTISTS, Artist::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating(),

                    DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating(),
                ]),

            Panel::make(__('nova.file_properties'), $this->fileProperties())
                ->collapsable(),

            Panel::make(__('nova.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }

    /**
     * @return array
     */
    protected function fileProperties(): array
    {
        return [
            Text::make(__('nova.path'), ImageModel::ATTRIBUTE_PATH)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new ImageUnlinkedLens(),
            ]
        );
    }
}

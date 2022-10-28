<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Models\Wiki\Studio as StudioModel;
use App\Nova\Actions\Models\Wiki\Studio\BackfillStudioAction;
use App\Nova\Lenses\Studio\Image\StudioCoverLargeLens;
use App\Nova\Lenses\Studio\Resource\StudioAniDbResourceLens;
use App\Nova\Lenses\Studio\Resource\StudioAnilistResourceLens;
use App\Nova\Lenses\Studio\Resource\StudioAnimePlanetResourceLens;
use App\Nova\Lenses\Studio\Resource\StudioAnnResourceLens;
use App\Nova\Lenses\Studio\Resource\StudioMalResourceLens;
use App\Nova\Lenses\Studio\StudioUnlinkedLens;
use App\Nova\Resources\BaseResource;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\StudioResource;
use Exception;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Studio.
 */
class Studio extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = StudioModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = StudioModel::ATTRIBUTE_NAME;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        return (string) data_get($this, StudioModel::ATTRIBUTE_SLUG);
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
        return __('nova.resources.group.wiki');
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
        return __('nova.resources.label.studios');
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
        return __('nova.resources.singularLabel.studio');
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
            new Column(StudioModel::ATTRIBUTE_NAME),
            new Column(StudioModel::ATTRIBUTE_SLUG),
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), StudioModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.studio.name.name'), StudioModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.fields.studio.name.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->showWhenPeeking(),

            Slug::make(__('nova.fields.studio.slug.name'), StudioModel::ATTRIBUTE_SLUG)
                ->from(StudioModel::ATTRIBUTE_NAME)
                ->separator('_')
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->updateRules(
                    Rule::unique(StudioModel::TABLE)
                        ->ignore($request->route('resourceId'), StudioModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->help(__('nova.fields.studio.slug.help'))
                ->showOnPreview()
                ->showWhenPeeking(),

            BelongsToMany::make(__('nova.resources.label.anime'), StudioModel::RELATION_ANIME, Anime::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating(),
                ]),

            BelongsToMany::make(__('nova.resources.label.external_resources'), StudioModel::RELATION_RESOURCES, ExternalResource::class)
                ->searchable()
                ->filterable()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.fields.studio.resources.as.name'), StudioResource::ATTRIBUTE_AS)
                        ->nullable()
                        ->copyable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.fields.studio.resources.as.help')),

                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            BelongsToMany::make(__('nova.resources.label.images'), StudioModel::RELATION_IMAGES, Image::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating(),
                ]),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return array_merge(
            parent::actions($request),
            [
                (new BackfillStudioAction($request->user()))
                    ->confirmButtonText(__('nova.actions.studio.backfill.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline()
                    ->canSeeWhen('update', StudioModel::class),
            ]
        );
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
                new StudioAniDbResourceLens(),
                new StudioAnilistResourceLens(),
                new StudioCoverLargeLens(),
                new StudioAnimePlanetResourceLens(),
                new StudioAnnResourceLens(),
                new StudioMalResourceLens(),
                new StudioUnlinkedLens(),
            ]
        );
    }
}

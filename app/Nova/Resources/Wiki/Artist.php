<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Models\Wiki\Artist as ArtistModel;
use App\Nova\Lenses\Artist\ArtistAniDbResourceLens;
use App\Nova\Lenses\Artist\ArtistAnilistResourceLens;
use App\Nova\Lenses\Artist\ArtistAnnResourceLens;
use App\Nova\Lenses\Artist\ArtistCoverLargeLens;
use App\Nova\Lenses\Artist\ArtistCoverSmallLens;
use App\Nova\Lenses\Artist\ArtistMalResourceLens;
use App\Nova\Lenses\Artist\ArtistSongLens;
use App\Nova\Metrics\Artist\ArtistsPerDay;
use App\Nova\Metrics\Artist\NewArtists;
use App\Nova\Resources\Resource;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use App\Pivots\BasePivot;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

/**
 * Class Artist.
 */
class Artist extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = ArtistModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = ArtistModel::ATTRIBUTE_NAME;

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
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.artists');
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
        return __('nova.artist');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        ArtistModel::ATTRIBUTE_NAME,
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), ArtistModel::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), ArtistModel::ATTRIBUTE_NAME)
                ->sortable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.artist_name_help'))
                ->showOnPreview()
                ->filterable(),

            Slug::make(__('nova.slug'), ArtistModel::ATTRIBUTE_SLUG)
                ->from(ArtistModel::ATTRIBUTE_NAME)
                ->separator('_')
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->updateRules(
                    Rule::unique(ArtistModel::TABLE)
                        ->ignore($request->route('resourceId'), ArtistModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->help(__('nova.artist_slug_help'))
                ->showOnPreview(),

            BelongsToMany::make(__('nova.songs'), 'Songs', Song::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), ArtistSong::ATTRIBUTE_AS)
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.external_resources'), 'Resources', ExternalResource::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), ArtistResource::ATTRIBUTE_AS)
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.members'), 'Members', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), ArtistMember::ATTRIBUTE_AS)
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.groups'), 'Groups', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), ArtistMember::ATTRIBUTE_AS)
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly(),
                    ];
                }),

            BelongsToMany::make(__('nova.images'), 'Images', Image::class)
                ->searchable()
                ->fields(function () {
                    return [
                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly(),
                    ];
                }),

            Panel::make(__('nova.timestamps'), $this->timestamps()),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request): array
    {
        return array_merge(
            parent::cards($request),
            [
                (new NewArtists())->width('1/2'),
                (new ArtistsPerDay())->width('1/2'),
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
                new ArtistAniDbResourceLens(),
                new ArtistAnilistResourceLens(),
                new ArtistAnnResourceLens(),
                new ArtistCoverLargeLens(),
                new ArtistCoverSmallLens(),
                new ArtistMalResourceLens(),
                new ArtistSongLens(),
            ]
        );
    }
}

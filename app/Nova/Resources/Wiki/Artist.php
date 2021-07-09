<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Nova\Lenses\ArtistAniDbResourceLens;
use App\Nova\Lenses\ArtistAnilistResourceLens;
use App\Nova\Lenses\ArtistAnnResourceLens;
use App\Nova\Lenses\ArtistCoverLargeLens;
use App\Nova\Lenses\ArtistCoverSmallLens;
use App\Nova\Lenses\ArtistMalResourceLens;
use App\Nova\Lenses\ArtistSongLens;
use App\Nova\Metrics\ArtistsPerDay;
use App\Nova\Metrics\NewArtists;
use App\Nova\Resources\Resource;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
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
    public static string $model = \App\Models\Wiki\Artist::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The logical group associated with the resource.
     *
     * @return array|string|null
     */
    public static function group(): array | string | null
    {
        return __('nova.wiki');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label(): array | string | null
    {
        return __('nova.artists');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel(): array | string | null
    {
        return __('nova.artist');
    }

    /**
     * The columns that should be searched.
     *
     * @var string[]
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'artist_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->rules('required', 'max:192')
                ->help(__('nova.artist_name_help')),

            Slug::make(__('nova.slug'), 'slug')
                ->from('name')
                ->separator('_')
                ->sortable()
                ->rules('required', 'max:192', 'alpha_dash')
                ->updateRules('unique:artist,slug,{{resourceId}},artist_id')
                ->help(__('nova.artist_slug_help')),

            BelongsToMany::make(__('nova.songs'), 'Songs', Song::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.external_resources'), 'Resources', ExternalResource::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.members'), 'Members', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.groups'), 'Groups', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules('nullable', 'max:192')
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly(),
                    ];
                }),

            BelongsToMany::make(__('nova.images'), 'Images', Image::class)
                ->searchable()
                ->fields(function () {
                    return [
                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly(),
                    ];
                }),

            AuditableLog::make(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request): array
    {
        return [
            (new NewArtists())->width('1/2'),
            (new ArtistsPerDay())->width('1/2'),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request): array
    {
        return [
            new ArtistAniDbResourceLens(),
            new ArtistAnilistResourceLens(),
            new ArtistAnnResourceLens(),
            new ArtistCoverLargeLens(),
            new ArtistCoverSmallLens(),
            new ArtistMalResourceLens(),
            new ArtistSongLens(),
        ];
    }
}
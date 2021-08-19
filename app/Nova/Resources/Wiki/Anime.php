<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Nova\Filters\Wiki\Anime\AnimeSeasonFilter;
use App\Nova\Filters\Wiki\Anime\AnimeYearFilter;
use App\Nova\Lenses\Anime\AnimeAniDbResourceLens;
use App\Nova\Lenses\Anime\AnimeAnilistResourceLens;
use App\Nova\Lenses\Anime\AnimeAnnResourceLens;
use App\Nova\Lenses\Anime\AnimeCoverLargeLens;
use App\Nova\Lenses\Anime\AnimeCoverSmallLens;
use App\Nova\Lenses\Anime\AnimeKitsuResourceLens;
use App\Nova\Lenses\Anime\AnimeMalResourceLens;
use App\Nova\Lenses\Anime\AnimePlanetResourceLens;
use App\Nova\Metrics\Anime\AnimePerDay;
use App\Nova\Metrics\Anime\NewAnime;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime\Synonym;
use App\Nova\Resources\Wiki\Anime\Theme;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;

/**
 * Class Anime.
 */
class Anime extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \App\Models\Wiki\Anime::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

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
        return __('nova.anime');
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
        return __('nova.anime');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function uriKey(): string
    {
        return 'anime';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
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
            ID::make(__('nova.id'), 'anime_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.anime_name_help')),

            Slug::make(__('nova.slug'), 'slug')
                ->from('name')
                ->separator('_')
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->updateRules('unique:anime,slug,{{resourceId}},anime_id')
                ->help(__('nova.anime_slug_help')),

            Number::make(__('nova.year'), 'year')
                ->sortable()
                ->min(1960)
                ->max(intval(date('Y')) + 1)
                ->rules(['required', 'digits:4', 'integer'])
                ->help(__('nova.anime_year_help')),

            Select::make(__('nova.season'), 'season')
                ->options(AnimeSeason::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum?->description;
                })
                ->sortable()
                ->rules(['required', (new EnumValue(AnimeSeason::class, false))->__toString()])
                ->help(__('nova.anime_season_help')),

            Textarea::make(__('nova.synopsis'), 'synopsis')
                ->rules('max:65535')
                ->nullable()
                ->help(__('nova.anime_synopsis_help')),

            HasMany::make(__('nova.synonyms'), 'AnimeSynonyms', Synonym::class),

            HasMany::make(__('nova.themes'), 'animethemes', Theme::class),

            BelongsToMany::make(__('nova.series'), 'Series', Series::class)
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

            BelongsToMany::make(__('nova.external_resources'), 'Resources', ExternalResource::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), 'as')
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.images'), 'Images', Image::class)
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
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request): array
    {
        return array_merge(
            parent::cards($request),
            [
                (new NewAnime())->width('1/2'),
                (new AnimePerDay())->width('1/2'),
            ]
        );
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            [
                new AnimeSeasonFilter(),
                new AnimeYearFilter(),
            ],
            parent::filters($request)
        );
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new AnimeAniDbResourceLens(),
                new AnimeAnilistResourceLens(),
                new AnimeCoverLargeLens(),
                new AnimeCoverSmallLens(),
                new AnimePlanetResourceLens(),
                new AnimeAnnResourceLens(),
                new AnimeKitsuResourceLens(),
                new AnimeMalResourceLens(),
            ]
        );
    }
}

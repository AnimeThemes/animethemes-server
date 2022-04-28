<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Auth\User;
use App\Models\Wiki\Anime as AnimeModel;
use App\Nova\Actions\Wiki\Anime\BackfillAnimeAction;
use App\Nova\Lenses\Anime\AnimeAniDbResourceLens;
use App\Nova\Lenses\Anime\AnimeAnilistResourceLens;
use App\Nova\Lenses\Anime\AnimeAnnResourceLens;
use App\Nova\Lenses\Anime\AnimeCoverLargeLens;
use App\Nova\Lenses\Anime\AnimeCoverSmallLens;
use App\Nova\Lenses\Anime\AnimeKitsuResourceLens;
use App\Nova\Lenses\Anime\AnimeMalResourceLens;
use App\Nova\Lenses\Anime\AnimePlanetResourceLens;
use App\Nova\Lenses\Anime\AnimeStudioLens;
use App\Nova\Metrics\Anime\AnimePerDay;
use App\Nova\Metrics\Anime\NewAnime;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime\Synonym;
use App\Nova\Resources\Wiki\Anime\Theme;
use App\Pivots\AnimeResource;
use App\Pivots\BasePivot;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Card;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

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
    public static string $model = AnimeModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = AnimeModel::ATTRIBUTE_NAME;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        return (string) data_get($this, AnimeModel::ATTRIBUTE_YEAR);
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
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new Column(AnimeModel::ATTRIBUTE_NAME),
        ];
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
            ID::make(__('nova.id'), AnimeModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), AnimeModel::ATTRIBUTE_NAME)
                ->sortable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.anime_name_help'))
                ->showOnPreview()
                ->filterable(),

            Slug::make(__('nova.slug'), AnimeModel::ATTRIBUTE_SLUG)
                ->from(AnimeModel::ATTRIBUTE_NAME)
                ->separator('_')
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->updateRules(
                    Rule::unique(AnimeModel::TABLE)
                        ->ignore($request->route('resourceId'), AnimeModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->help(__('nova.anime_slug_help'))
                ->showOnPreview(),

            Number::make(__('nova.year'), AnimeModel::ATTRIBUTE_YEAR)
                ->sortable()
                ->min(1960)
                ->max(intval(date('Y')) + 1)
                ->rules(['required', 'digits:4', 'integer'])
                ->help(__('nova.anime_year_help'))
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.season'), AnimeModel::ATTRIBUTE_SEASON)
                ->options(AnimeSeason::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(AnimeSeason::class, false))->__toString()])
                ->help(__('nova.anime_season_help'))
                ->showOnPreview()
                ->filterable(),

            Textarea::make(__('nova.synopsis'), AnimeModel::ATTRIBUTE_SYNOPSIS)
                ->rules('max:65535')
                ->nullable()
                ->help(__('nova.anime_synopsis_help'))
                ->showOnPreview(),

            HasMany::make(__('nova.anime_synonyms'), AnimeModel::RELATION_SYNONYMS, Synonym::class),

            HasMany::make(__('nova.anime_themes'), AnimeModel::RELATION_THEMES, Theme::class),

            BelongsToMany::make(__('nova.series'), AnimeModel::RELATION_SERIES, Series::class)
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

            BelongsToMany::make(__('nova.external_resources'), AnimeModel::RELATION_RESOURCES, ExternalResource::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.as'), AnimeResource::ATTRIBUTE_AS)
                        ->nullable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.resource_as_help')),

                    DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            BelongsToMany::make(__('nova.images'), AnimeModel::RELATION_IMAGES, Image::class)
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

            BelongsToMany::make(__('nova.studios'), AnimeModel::RELATION_STUDIOS, Studio::class)
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

            Panel::make(__('nova.timestamps'), $this->timestamps())
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
                (new BackfillAnimeAction($request->user()))
                    ->confirmButtonText(__('nova.backfill'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->hasCurrentTeamPermission('anime:update');
                    }),
            ]
        );
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
                (new NewAnime())->width(Card::ONE_HALF_WIDTH),
                (new AnimePerDay())->width(Card::ONE_HALF_WIDTH),
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
                new AnimeAniDbResourceLens(),
                new AnimeAnilistResourceLens(),
                new AnimeCoverLargeLens(),
                new AnimeCoverSmallLens(),
                new AnimePlanetResourceLens(),
                new AnimeAnnResourceLens(),
                new AnimeKitsuResourceLens(),
                new AnimeMalResourceLens(),
                new AnimeStudioLens(),
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Song as SongModel;
use App\Nova\Lenses\Song\SongArtistLens;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Anime\Theme;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\ArtistSong;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Song.
 */
class Song extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = SongModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = SongModel::ATTRIBUTE_TITLE;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        $song = $this->model();
        if ($song instanceof SongModel) {
            $theme = $song->animethemes->first();
            if ($theme instanceof AnimeTheme) {
                return $theme->anime->getName();
            }
        }

        return null;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function relatableQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(SongModel::RELATION_ANIME);
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(SongModel::RELATION_ANIME);
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
        return __('nova.resources.label.songs');
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
        return __('nova.resources.singularLabel.song');
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
            new Column(SongModel::ATTRIBUTE_TITLE),
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
            ID::make(__('nova.fields.base.id'), SongModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.song.title.name'), SongModel::ATTRIBUTE_TITLE)
                ->sortable()
                ->copyable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.fields.song.title.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            BelongsToMany::make(__('nova.resources.label.artists'), SongModel::RELATION_ARTISTS, Artist::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.fields.artist.songs.as.name'), ArtistSong::ATTRIBUTE_AS)
                        ->nullable()
                        ->copyable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.fields.artist.songs.as.help')),

                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            HasMany::make(__('nova.resources.label.anime_themes'), SongModel::RELATION_ANIMETHEMES, Theme::class),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
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
                new SongArtistLens(),
            ]
        );
    }
}

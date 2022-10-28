<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki\Anime\Theme;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime\Theme;
use App\Nova\Resources\Wiki\Video;
use App\Pivots\BasePivot;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Entry.
 */
class Entry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = AnimeThemeEntry::class;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function title(): string
    {
        $entry = $this->model();
        if ($entry instanceof AnimeThemeEntry) {
            if (is_int($entry->version)) {
                return "V$entry->version";
            }

            return "{$entry->getKey()}";
        }

        return '';
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        $entry = $this->model();
        if ($entry instanceof AnimeThemeEntry) {
            return "{$entry->anime->getName()} {$entry->animetheme->getName()}";
        }

        return null;
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
        return __('nova.resources.label.anime_theme_entries');
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
        return __('nova.resources.singularLabel.anime_theme_entry');
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
        return 'anime-theme-entries';
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
            new Column(AnimeThemeEntry::ATTRIBUTE_ID),
        ];
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
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->with([AnimeThemeEntry::RELATION_THEME, AnimeThemeEntry::RELATION_ANIME_SHALLOW]);
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
        return $query->with([AnimeThemeEntry::RELATION_THEME, AnimeThemeEntry::RELATION_ANIME_SHALLOW]);
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
            BelongsTo::make(__('nova.resources.singularLabel.anime'), AnimeThemeEntry::RELATION_ANIME_SHALLOW, Anime::class)
                ->sortable()
                ->hideFromIndex(fn () => $request->viaResource() !== null && Video::class !== $request->viaResource())
                ->hideWhenCreating()
                ->readonly()
                ->showOnPreview(),

            BelongsTo::make(__('nova.resources.singularLabel.anime_theme'), AnimeThemeEntry::RELATION_THEME, Theme::class)
                ->sortable()
                ->filterable()
                ->searchable(fn () => $request->viaResource() === null)
                ->readonly(fn () => $request->viaResource() !== null)
                ->required(fn () => $request->viaResource() === null)
                ->withSubtitles()
                ->showOnPreview(),

            ID::make(__('nova.fields.base.id'), AnimeThemeEntry::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Number::make(__('nova.fields.anime_theme_entry.version.name'), AnimeThemeEntry::ATTRIBUTE_VERSION)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.fields.anime_theme_entry.version.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.anime_theme_entry.episodes.name'), AnimeThemeEntry::ATTRIBUTE_EPISODES)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.fields.anime_theme_entry.episodes.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->showWhenPeeking(),

            Boolean::make(__('nova.fields.anime_theme_entry.nsfw.name'), AnimeThemeEntry::ATTRIBUTE_NSFW)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.fields.anime_theme_entry.nsfw.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Boolean::make(__('nova.fields.anime_theme_entry.spoiler.name'), AnimeThemeEntry::ATTRIBUTE_SPOILER)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.fields.anime_theme_entry.spoiler.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.anime_theme_entry.notes.name'), AnimeThemeEntry::ATTRIBUTE_NOTES)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.fields.anime_theme_entry.notes.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->showWhenPeeking(),

            BelongsToMany::make(__('nova.resources.label.videos'), AnimeThemeEntry::RELATION_VIDEOS, Video::class)
                ->searchable()
                ->filterable()
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
}

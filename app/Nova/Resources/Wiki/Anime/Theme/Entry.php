<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki\Anime\Theme;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime\Theme;
use App\Nova\Resources\Wiki\Video;
use App\Pivots\BasePivot;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

/**
 * Class Entry.
 */
class Entry extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = AnimeThemeEntry::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = AnimeThemeEntry::ATTRIBUTE_ID;

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.entries');
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
        return __('nova.entry');
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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        AnimeThemeEntry::ATTRIBUTE_ID,
    ];

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            BelongsTo::make(__('nova.anime'), 'Anime', Anime::class)
                ->hideFromIndex(fn () => Video::class !== $request->viaResource())
                ->readonly()
                ->showOnPreview(),

            BelongsTo::make(__('nova.theme'), 'AnimeTheme', Theme::class)
                ->readonly()
                ->showOnPreview(),

            ID::make(__('nova.id'), 'entry_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable()
                ->showOnPreview(),

            Number::make(__('nova.version'), AnimeThemeEntry::ATTRIBUTE_VERSION)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.entry_version_help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.episodes'), AnimeThemeEntry::ATTRIBUTE_EPISODES)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.entry_episodes_help'))
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.nsfw'), AnimeThemeEntry::ATTRIBUTE_NSFW)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.entry_nsfw_help'))
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.spoiler'), AnimeThemeEntry::ATTRIBUTE_SPOILER)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.entry_spoiler_help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.notes'), AnimeThemeEntry::ATTRIBUTE_NOTES)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.entry_notes_help'))
                ->showOnPreview()
                ->filterable(),

            BelongsToMany::make(__('nova.videos'), 'Videos', Video::class)
                ->searchable()
                ->fields(function () {
                    return [
                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            Panel::make(__('nova.timestamps'), $this->timestamps()),
        ];
    }
}

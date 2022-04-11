<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use App\Nova\Resources\Wiki\Song;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Theme.
 */
class Theme extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = AnimeTheme::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = AnimeTheme::ATTRIBUTE_SLUG;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.themes');
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
        return __('nova.theme');
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
        return 'anime-themes';
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
            new Column(AnimeTheme::ATTRIBUTE_SLUG),
        ];
    }

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), AnimeTheme::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            BelongsTo::make(__('nova.anime'), 'Anime', Anime::class)
                ->sortable()
                ->searchable(fn () => Song::class === $request->viaResource())
                ->readonly(fn () => Song::class !== $request->viaResource())
                ->required(fn () => Song::class === $request->viaResource())
                ->withSubtitles()
                ->showCreateRelationButton(fn () => Song::class === $request->viaResource())
                ->showOnPreview(),

            Select::make(__('nova.type'), AnimeTheme::ATTRIBUTE_TYPE)
                ->options(ThemeType::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(ThemeType::class, false))->__toString()])
                ->help(__('nova.theme_type_help'))
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.sequence'), AnimeTheme::ATTRIBUTE_SEQUENCE)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.theme_sequence_help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.group'), AnimeTheme::ATTRIBUTE_GROUP)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.theme_group_help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.slug'), AnimeTheme::ATTRIBUTE_SLUG)
                ->hideWhenCreating()
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->help(__('nova.theme_slug_help'))
                ->showOnPreview()
                ->filterable(),

            BelongsTo::make(__('nova.song'), 'Song', Song::class)
                ->sortable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showCreateRelationButton()
                ->showOnPreview(),

            HasMany::make(__('nova.entries'), 'AnimeThemeEntries', Entry::class),

            Panel::make(__('nova.timestamps'), $this->timestamps()),
        ];
    }
}

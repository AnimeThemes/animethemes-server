<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Nova\Filters\Wiki\Anime\Theme\ThemeTypeFilter;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use App\Nova\Resources\Wiki\Song;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        AnimeTheme::ATTRIBUTE_SLUG,
    ];

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
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), AnimeTheme::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            BelongsTo::make(__('nova.anime'), 'Anime', Anime::class)
               ->readonly(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Select::make(__('nova.type'), AnimeTheme::ATTRIBUTE_TYPE)
                ->options(ThemeType::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum?->description;
                })
                ->sortable()
                ->rules(['required', (new EnumValue(ThemeType::class, false))->__toString()])
                ->help(__('nova.theme_type_help')),

            Number::make(__('nova.sequence'), AnimeTheme::ATTRIBUTE_SEQUENCE)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.theme_sequence_help')),

            Text::make(__('nova.group'), AnimeTheme::ATTRIBUTE_GROUP)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.theme_group_help')),

            Text::make(__('nova.slug'), AnimeTheme::ATTRIBUTE_SLUG)
                ->hideWhenCreating()
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->help(__('nova.theme_slug_help')),

            BelongsTo::make(__('nova.song'), 'Song', Song::class)
                ->sortable()
                ->searchable()
                ->nullable()
                ->showCreateRelationButton(),

            HasMany::make(__('nova.entries'), 'AnimeThemeEntries', Entry::class),

            AuditableLog::make(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            [
                new ThemeTypeFilter(),
            ],
            parent::filters($request)
        );
    }
}

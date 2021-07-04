<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\ThemeType;
use App\Nova\Filters\Wiki\ThemeTypeFilter;
use App\Nova\Resources\Resource;
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
    public static string $model = \App\Models\Wiki\Theme::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'slug';

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label(): array | string | null
    {
        return __('nova.themes');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel(): array | string | null
    {
        return __('nova.theme');
    }

    /**
     * The columns that should be searched.
     *
     * @var string[]
     */
    public static $search = [
        'slug',
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
     * @param Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'theme_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            BelongsTo::make(__('nova.anime'), 'Anime', Anime::class)
               ->readonly(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Select::make(__('nova.type'), 'type')
                ->options(ThemeType::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('required', (new EnumValue(ThemeType::class, false))->__toString())
                ->help(__('nova.theme_type_help')),

            Number::make(__('nova.sequence'), 'sequence')
                ->sortable()
                ->nullable()
                ->rules('nullable', 'integer')
                ->help(__('nova.theme_sequence_help')),

            Text::make(__('nova.group'), 'group')
                ->sortable()
                ->nullable()
                ->rules('nullable', 'max:192')
                ->help(__('nova.theme_group_help')),

            Text::make(__('nova.slug'), 'slug')
                ->hideWhenCreating()
                ->sortable()
                ->rules('required', 'max:192', 'alpha_dash')
                ->help(__('nova.theme_slug_help')),

            BelongsTo::make(__('nova.song'), 'Song', Song::class)
                ->sortable()
                ->searchable()
                ->nullable()
                ->showCreateRelationButton(),

            HasMany::make(__('nova.entries'), 'Entries', Entry::class),

            AuditableLog::make(),
        ];
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
                new ThemeTypeFilter(),
            ],
            parent::filters($request)
        );
    }
}

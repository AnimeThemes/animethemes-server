<?php

declare(strict_types=1);

namespace App\Nova\Resources\Admin;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Admin\FeaturedTheme as FeaturedThemeModel;
use App\Models\Wiki\Video as VideoModel;
use App\Nova\Resources\Auth\User;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use App\Nova\Resources\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\SearchableRelation;

/**
 * Class FeaturedTheme.
 */
class FeaturedTheme extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = FeaturedThemeModel::class;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        $featuredTheme = $this->model();
        if ($featuredTheme instanceof FeaturedThemeModel) {
            return $featuredTheme->getName();
        }

        return parent::title();
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
        return __('nova.resources.group.admin');
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
        return __('nova.resources.label.featured_themes');
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
        return __('nova.resources.singularLabel.featured_theme');
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
            new SearchableRelation(FeaturedThemeModel::RELATION_VIDEO, VideoModel::ATTRIBUTE_FILENAME),
        ];
    }

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Determine if this resource uses Laravel Scout.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function usesScout(): bool
    {
        return false;
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
        return $query->with([
            FeaturedThemeModel::RELATION_ENTRY,
            FeaturedThemeModel::RELATION_USER,
            FeaturedThemeModel::RELATION_VIDEO,
        ]);
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
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return [
            ID::make(__('nova.fields.base.id'), FeaturedThemeModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Date::make(__('nova.fields.featured_theme.start_at.name'), FeaturedThemeModel::ATTRIBUTE_START_AT)
                ->sortable()
                ->required()
                ->rules([
                    'required',
                    Str::of('date_format:')
                        ->append(implode(',', $allowedDateFormats))
                        ->__toString(),
                    Str::of('before:')
                        ->append(FeaturedThemeModel::ATTRIBUTE_END_AT)
                        ->__toString(),
                ])
                ->help(__('nova.fields.featured_theme.start_at.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Date::make(__('nova.fields.featured_theme.end_at.name'), FeaturedThemeModel::ATTRIBUTE_END_AT)
                ->sortable()
                ->required()
                ->rules([
                    'required',
                    Str::of('date_format:')
                        ->append(implode(',', $allowedDateFormats))
                        ->__toString(),
                    Str::of('after:')
                        ->append(FeaturedThemeModel::ATTRIBUTE_START_AT)
                        ->__toString(),
                ])
                ->help(__('nova.fields.featured_theme.end_at.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.resources.singularLabel.video'), FeaturedThemeModel::RELATION_VIDEO, Video::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->rules(fn (NovaRequest $request) => [
                    Rule::when(
                        ! empty($request->get(FeaturedThemeModel::RELATION_ENTRY)) && ! empty($request->get(FeaturedThemeModel::RELATION_VIDEO)),
                        [
                            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                                ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $request->get(FeaturedThemeModel::RELATION_ENTRY)),
                        ]
                    ),
                ])
                ->showOnPreview(),

            BelongsTo::make(__('nova.resources.singularLabel.anime_theme_entry'), FeaturedThemeModel::RELATION_ENTRY, Entry::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->rules(fn (NovaRequest $request) => [
                    Rule::when(
                        ! empty($request->get(FeaturedThemeModel::RELATION_ENTRY)) && ! empty($request->get(FeaturedThemeModel::RELATION_VIDEO)),
                        [
                            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                                ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $request->get(FeaturedThemeModel::RELATION_VIDEO)),
                        ]
                    ),
                ])
                ->showOnPreview(),

            BelongsTo::make(__('nova.resources.singularLabel.user'), FeaturedThemeModel::RELATION_USER, User::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->showOnPreview(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}

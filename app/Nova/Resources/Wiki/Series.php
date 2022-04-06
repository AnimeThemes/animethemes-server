<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Models\Wiki\Series as SeriesModel;
use App\Nova\Metrics\Series\NewSeries;
use App\Nova\Metrics\Series\SeriesPerDay;
use App\Nova\Resources\Resource;
use App\Pivots\BasePivot;
use Illuminate\Validation\Rule;
use Laravel\Nova\Card;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

/**
 * Class Series.
 */
class Series extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = SeriesModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = SeriesModel::ATTRIBUTE_NAME;

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
        return __('nova.series');
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
        return __('nova.series');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        SeriesModel::ATTRIBUTE_NAME,
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), SeriesModel::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), SeriesModel::ATTRIBUTE_NAME)
                ->sortable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.series_name_help'))
                ->showOnPreview()
                ->filterable(),

            Slug::make(__('nova.slug'), SeriesModel::ATTRIBUTE_SLUG)
                ->from(SeriesModel::ATTRIBUTE_NAME)
                ->separator('_')
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->updateRules(
                    Rule::unique(SeriesModel::TABLE)
                        ->ignore($request->route('resourceId'), SeriesModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->help(__('nova.series_slug_help'))
                ->showOnPreview(),

            BelongsToMany::make(__('nova.anime'), 'Anime', Anime::class)
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
                (new NewSeries())->width(Card::ONE_HALF_WIDTH),
                (new SeriesPerDay())->width(Card::ONE_HALF_WIDTH),
            ]
        );
    }
}

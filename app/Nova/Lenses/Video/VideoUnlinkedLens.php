<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use App\Nova\Lenses\BaseLens;
use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class VideoUnlinkedLens.
 */
class VideoUnlinkedLens extends BaseLens
{
    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.video_unlinked_lens');
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Video::RELATION_ANIMETHEMEENTRIES)
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE, 'misc%');
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), Video::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.filename'), Video::ATTRIBUTE_FILENAME)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.resolution'), Video::ATTRIBUTE_RESOLUTION)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.nc'), Video::ATTRIBUTE_NC)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.subbed'), Video::ATTRIBUTE_SUBBED)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.lyrics'), Video::ATTRIBUTE_LYRICS)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.uncen'), Video::ATTRIBUTE_UNCEN)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.overlap'), Video::ATTRIBUTE_OVERLAP)
                ->options(VideoOverlap::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->onlyOnPreview(),

            Select::make(__('nova.source'), Video::ATTRIBUTE_SOURCE)
                ->options(VideoSource::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->onlyOnPreview(),

            DateTime::make(__('nova.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function uriKey(): string
    {
        return 'video-unlinked-lens';
    }
}

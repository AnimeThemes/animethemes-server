<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use App\Nova\Lenses\BaseLens;
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
        return __('nova.lenses.video.unlinked.name');
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
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE->value, 'misc%');
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
            ID::make(__('nova.fields.base.id'), Video::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.fields.video.filename.name'), Video::ATTRIBUTE_FILENAME)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.fields.video.resolution.name'), Video::ATTRIBUTE_RESOLUTION)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.fields.video.nc.name'), Video::ATTRIBUTE_NC)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.fields.video.subbed.name'), Video::ATTRIBUTE_SUBBED)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.fields.video.lyrics.name'), Video::ATTRIBUTE_LYRICS)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.fields.video.uncen.name'), Video::ATTRIBUTE_UNCEN)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.fields.video.overlap.name'), Video::ATTRIBUTE_OVERLAP)
                ->options(VideoOverlap::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => VideoOverlap::tryFrom($enumValue)?->localize())
                ->onlyOnPreview(),

            Select::make(__('nova.fields.video.source.name'), Video::ATTRIBUTE_SOURCE)
                ->options(VideoSource::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => VideoSource::tryFrom($enumValue)?->localize())
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
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

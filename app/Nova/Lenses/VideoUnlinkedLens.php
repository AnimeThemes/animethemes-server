<?php declare(strict_types=1);

namespace App\Nova\Lenses;

use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Filters\VideoTypeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

/**
 * Class VideoUnlinkedLens
 * @package App\Nova\Lenses
 */
class VideoUnlinkedLens extends Lens
{
    /**
     * Get the displayable name of the lens.
     *
     * @return array|string|null
     */
    public function name(): array|string|null
    {
        return __('nova.video_unlinked_lens');
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param LensRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function query(LensRequest $request, $query): Builder
    {
        return $request->withOrdering($request->withFilters(
            $query->whereDoesntHave('entries')
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'video_id')
                ->sortable(),

            Text::make(__('nova.filename'), 'filename')
                ->sortable(),

            Number::make(__('nova.resolution'), 'resolution')
                ->sortable(),

            Boolean::make(__('nova.nc'), 'nc')
                ->sortable(),

            Boolean::make(__('nova.subbed'), 'subbed')
                ->sortable(),

            Boolean::make(__('nova.lyrics'), 'lyrics')
                ->sortable(),

            Boolean::make(__('nova.uncen'), 'uncen')
                ->sortable(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [
            new VideoTypeFilter(),
            new CreatedStartDateFilter(),
            new CreatedEndDateFilter(),
            new UpdatedStartDateFilter(),
            new UpdatedEndDateFilter(),
            new DeletedStartDateFilter(),
            new DeletedEndDateFilter(),
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey(): string
    {
        return 'video-unlinked-lens';
    }
}

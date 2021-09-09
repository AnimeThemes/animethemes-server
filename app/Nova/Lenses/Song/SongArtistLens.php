<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Song;

use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

/**
 * Class SongArtistLens.
 */
class SongArtistLens extends Lens
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
        return __('nova.song_artist_lens');
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  LensRequest  $request
     * @param  Builder  $query
     * @return Builder
     */
    public static function query(LensRequest $request, $query): Builder
    {
        return $request->withOrdering($request->withFilters(
            $query->whereDoesntHave('artists')
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'song_id')
                ->sortable(),

            Text::make(__('nova.title'), 'title')
                ->sortable(),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            parent::filters($request),
            [
                new CreatedStartDateFilter(),
                new CreatedEndDateFilter(),
                new UpdatedStartDateFilter(),
                new UpdatedEndDateFilter(),
                new DeletedStartDateFilter(),
                new DeletedEndDateFilter(),
            ]
        );
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(Request $request): array
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
        return 'song-artist-lens';
    }
}

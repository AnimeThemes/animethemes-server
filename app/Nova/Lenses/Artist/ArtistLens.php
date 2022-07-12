<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Artist;

use App\Models\BaseModel;
use App\Models\Wiki\Artist;
use App\Nova\Lenses\BaseLens;
use Exception;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ArtistLens.
 */
abstract class ArtistLens extends BaseLens
{
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
            ID::make(__('nova.id'), Artist::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), Artist::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.slug'), Artist::ATTRIBUTE_SLUG)
                ->sortable()
                ->copyable()
                ->showOnPreview(),

            DateTime::make(__('nova.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
        ];
    }
}

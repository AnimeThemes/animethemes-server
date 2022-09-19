<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Studio;

use App\Models\BaseModel;
use App\Models\Wiki\Studio;
use App\Nova\Lenses\BaseLens;
use Exception;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class StudioLens.
 */
abstract class StudioLens extends BaseLens
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
            ID::make(__('nova.fields.base.id'), Studio::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.fields.studio.name.name'), Studio::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.fields.studio.slug.name'), Studio::ATTRIBUTE_SLUG)
                ->sortable()
                ->copyable()
                ->showOnPreview(),

            DateTime::make(__('nova.fields.base.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
        ];
    }
}

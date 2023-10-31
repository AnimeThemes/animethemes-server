<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Song;

use App\Models\BaseModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Song;
use App\Nova\Lenses\BaseLens;
use App\Nova\Resources\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime\Theme;
use Exception;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class SongLens.
 */
abstract class SongLens extends BaseLens
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
            ID::make(__('nova.fields.base.id'), Song::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.fields.song.title.name'), Song::ATTRIBUTE_TITLE)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            DateTime::make(__('nova.fields.base.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
        ];
    }
}

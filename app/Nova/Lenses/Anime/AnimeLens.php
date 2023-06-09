<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime as AnimeModel;
use App\Nova\Lenses\BaseLens;
use Exception;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AnimeLens.
 */
abstract class AnimeLens extends BaseLens
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
            ID::make(__('nova.fields.base.id'), Anime::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.fields.anime.name.name'), Anime::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.fields.anime.slug.name'), Anime::ATTRIBUTE_SLUG)
                ->sortable()
                ->copyable()
                ->showOnPreview(),

            Number::make(__('nova.fields.anime.year.name'), Anime::ATTRIBUTE_YEAR)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.fields.anime.season.name'), Anime::ATTRIBUTE_SEASON)
                ->options(AnimeSeason::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => AnimeSeason::tryFrom($enumValue)?->localize())
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Textarea::make(__('nova.fields.anime.synopsis.name'), AnimeModel::ATTRIBUTE_SYNOPSIS)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
        ];
    }
}

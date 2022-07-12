<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime as AnimeModel;
use App\Nova\Lenses\BaseLens;
use BenSampo\Enum\Enum;
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
            ID::make(__('nova.id'), Anime::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), Anime::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.slug'), Anime::ATTRIBUTE_SLUG)
                ->sortable()
                ->copyable()
                ->showOnPreview(),

            Number::make(__('nova.year'), Anime::ATTRIBUTE_YEAR)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.season'), Anime::ATTRIBUTE_SEASON)
                ->options(AnimeSeason::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->showOnPreview()
                ->filterable(),

            Textarea::make(__('nova.synopsis'), AnimeModel::ATTRIBUTE_SYNOPSIS)
                ->onlyOnPreview(),

            DateTime::make(__('nova.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
        ];
    }
}
